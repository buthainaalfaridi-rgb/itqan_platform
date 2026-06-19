<?php

set_time_limit(0);
ini_set('max_execution_time', 0);

include 'components/connect.php';

require __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use Dotenv\Dotenv;

/* =====================================================
   USER CHECK
===================================================== */

$user_id = $_COOKIE['user_id'] ?? '';

if(!$user_id){
    header('location:login.php');
    exit();
}

if(!isset($_GET['file_id'])){
    header('location:upload.php');
    exit();
}

$file_id = (int)$_GET['file_id'];

/* =====================================================
   FETCH FILE
===================================================== */

$stmt = $conn->prepare("
    SELECT *
    FROM upload
    WHERE id = ?
    AND user_id = ?
");

$stmt->execute([$file_id, $user_id]);

if($stmt->rowCount() == 0){
    die('الملف غير موجود');
}

$file = $stmt->fetch(PDO::FETCH_ASSOC);
$file_path = $file['file_path'];

/* =====================================================
   LOG FUNCTION
===================================================== */

function writeLog($message){

    if(!is_dir('logs')){
        mkdir('logs', 0777, true);
    }

    file_put_contents(
        'logs/gemini_questions.log',
        "[" . date('Y-m-d H:i:s') . "] " . $message . PHP_EOL,
        FILE_APPEND
    );
}

/* =====================================================
   CLEAN TEXT
===================================================== */

function cleanFileText($text){

    $text = preg_replace('/[^\p{Arabic}\p{Latin}\p{N}\s\.\,\-\:\;\?\!\(\)]/u', ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

/* =====================================================
   SPLIT CHUNKS
===================================================== */

function splitTextIntoChunks($text, $chunkSize = 4000){

    $paragraphs = preg_split('/(?<=[\.\!\؟])\s+/u', $text);

    $chunks = [];
    $current = '';

    foreach($paragraphs as $paragraph){

        if(mb_strlen($current . $paragraph) > $chunkSize){
            $chunks[] = trim($current);
            $current = '';
        }

        $current .= $paragraph . " ";
    }

    if(!empty(trim($current))){
        $chunks[] = trim($current);
    }

    return $chunks;
}

/* =====================================================
   GEMINI REQUEST (QUESTIONS VERSION)
===================================================== */

function callGemini($chunk, $apiKey, $fileName){

    $models = [
        'gemini-2.5-flash',
        'gemini-2.5-pro',
        'gemini-2.0-flash'
    ];

    foreach($models as $model){

        $url =
        "https://generativelanguage.googleapis.com/v1beta/models/"
        .$model.
        ":generateContent?key=".$apiKey;

        $prompt = "

أنت نظام ذكي لإنشاء اختبارات تعليمية.

اسم الملف:
".$fileName."

المطلوب:

- أنشئ من 5 إلى 10 أسئلة اختيار من متعدد فقط
- أرجع HTML فقط بدون أي نص إضافي
- كل سؤال داخل div class='card'
- الإجابة الصحيحة فقط عليها class='correct-answer'
- لا تستخدم Markdown
- لا تضف شرح أو مقدمة

الشكل:

<div class='card'>
<h3>السؤال</h3>
<ul>
<li>خيار 1</li>
<li class='correct-answer'>الإجابة الصحيحة</li>
<li>خيار 3</li>
<li>خيار 4</li>
</ul>
</div>

النص:
".$chunk;

        $data = [
            "contents" => [[
                "parts" => [[
                    "text" => $prompt
                ]]
            ]]
        ];

        $attempt = 0;

        while($attempt < 5){

            $attempt++;

            $ch = curl_init();

            curl_setopt_array($ch, [

                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_CONNECTTIMEOUT => 15,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json"
                ],
                CURLOPT_POSTFIELDS => json_encode($data)
            ]);

            $response = curl_exec($ch);
            $curlError = curl_error($ch);

            curl_close($ch);

            if($curlError){
                writeLog("CURL ERROR [$model]: ".$curlError);
                sleep($attempt * 2);
                continue;
            }

            writeLog("[$model] ".substr($response,0,2000));

            $result = json_decode($response, true);

            $errorMessage = $result['error']['message'] ?? '';

            if(
                str_contains($errorMessage,'429') ||
                str_contains($errorMessage,'quota') ||
                str_contains($errorMessage,'overloaded') ||
                str_contains($errorMessage,'high demand')
            ){
                sleep($attempt * 3);
                continue;
            }

            if(isset($result['error'])){
                writeLog("API ERROR [$model]: ".$errorMessage);
                break;
            }

            $text =
            $result['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if(empty($text)){
                sleep($attempt * 2);
                continue;
            }

            $text = preg_replace('/^```html\s*/i','',$text);
            $text = preg_replace('/```$/','',$text);
            $text = preg_replace('#<script(.*?)>(.*?)</script>#is','',$text);
            $text = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is','',$text);
            $text = preg_replace('#<form(.*?)>(.*?)</form>#is','',$text);

            return [
                'success' => true,
                'text' => trim($text)
            ];
        }
    }

    return [
        'success' => false,
        'message' => 'فشل الاتصال بـ Gemini'
    ];
}

/* =====================================================
   LOAD ENV
===================================================== */

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiKey = $_ENV['GEMINI_API_KEY'] ?? '';

if(empty($apiKey)){
    die('GEMINI_API_KEY غير موجود');
}

/* =====================================================
   CHECK CACHE (NEW COLUMN)
===================================================== */

$ai_output = '';

if(!empty($file['ai_questions'])){
    $ai_output = $file['ai_questions'];

}else{

    if(!file_exists($file_path)){
        die('الملف غير موجود');
    }

    if(filesize($file_path) < 10){
        die('الملف فارغ');
    }

    if(filesize($file_path) > 15 * 1024 * 1024){
        die('حجم الملف كبير جدًا');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file_path);

    $allowed = [
        'application/pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/msword',
        'text/plain'
    ];

    if(!in_array($mime, $allowed)){
        die('نوع الملف غير مدعوم');
    }

    /* =====================================================
       EXTRACT TEXT
    ===================================================== */

    $pdf_text = '';

    try{

        if($mime == 'application/pdf'){

            $parser = new Parser();
            $pdf = $parser->parseFile($file_path);
            $pdf_text = $pdf->getText();

        }elseif($mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'){

            $phpWord = IOFactory::load($file_path);

            foreach($phpWord->getSections() as $section){
                foreach($section->getElements() as $element){
                    if(method_exists($element,'getText')){
                        $pdf_text .= $element->getText()."\n";
                    }
                }
            }

        }elseif($mime == 'application/msword'){
            $pdf_text = strip_tags(file_get_contents($file_path));
        }else{
            $pdf_text = file_get_contents($file_path);
        }

    }catch(Exception $e){
        die('فشل قراءة الملف: '.$e->getMessage());
    }

    if(empty(trim($pdf_text))){
        die('تعذر استخراج النص');
    }

    /* =====================================================
       CLEAN + CHUNKS
    ===================================================== */

    $pdf_text = cleanFileText($pdf_text);
    $pdf_text = mb_substr($pdf_text, 0, 15000);

    $chunks = splitTextIntoChunks($pdf_text, 4000);

    $final_output = '';

    foreach($chunks as $index => $chunk){

        sleep(2);

        $result = callGemini($chunk, $apiKey, $file['file_name']);

        if($result['success']){
            $final_output .= $result['text']."\n";
        }else{
            $final_output .= "<div style='background: #fff0f0;color: #b30000;padding:20px;border-radius:15px;margin-bottom:20px;'>
            فشل الجزء ".($index+1)." <br>".$result['message']."</div>";
        }
    }

    $ai_output = $final_output;

    /* =====================================================
       SAVE TO ai_questions
    ===================================================== */

    $update = $conn->prepare("
        UPDATE upload
        SET ai_questions = ?
        WHERE id = ?
    ");

    $update->execute([
        $ai_output,
        $file_id
    ]);
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>

   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>إنشاء أسئلة الملف</title>
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

<style>

body{
    font-family:'Cairo',sans-serif;

}

.ai-summary-container{
 
    padding:20px;
    border-radius:20px;
    margin-top:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.07);
}

.card{
    background: #35abaf70;
    padding:20px;
    margin-bottom:15px;
    border-radius:12px;
}

.card ul{
    list-style:none;
    padding:0;
}

.card li{
    background: #fff;
    padding:8px;
    margin-bottom:6px;
    border-radius:6px;
}

.correct-answer{
    display:none;
}

.show .correct-answer{
    display:block;
    background: #ffe0e0;
    color: #2561af80;
    font-weight:bold;
}

.summary-actions{
    margin-top:20px;
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

</style>

<script>
function showAnswers(){
    document.querySelectorAll('.card').forEach(c=>{
        c.classList.add('show');
    });
}
</script>

</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="playlist">

<h1 class="heading">إنشاء أسئلة الملف</h1>

<div class="details">

<h2><?= htmlspecialchars($file['file_name']); ?></h2>

<div class="ai-summary-container">

<?= $ai_output; ?>

</div>

<div class="summary-actions">

<a href="upload.php" class="inline-btn">رجوع</a>
<a href="<?= $file['file_path']; ?>" target="_blank" class="inline-btn">قراءة الملف</a>
<a href="summary.php?file_id=<?= $file_id; ?>" class="inline-btn">الملخص</a>

<button onclick="showAnswers()" class="inline-option-btn">
إظهار الإجابات
</button>

</div>

</div>

</section>

<script src="js/script.js"></script>

<script src="js/switcher.js"></script>
</body>
</html>