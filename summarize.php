
<?php

set_time_limit(0);

ini_set('max_execution_time', 0);

include 'components/connect.php';

require __DIR__ . '/vendor/autoload.php';

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use Dotenv\Dotenv;
use Mpdf\Mpdf;

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
   FUNCTIONS
===================================================== */

function writeLog($message){

    if(!is_dir('logs')){

        mkdir('logs', 0777, true);
    }

    file_put_contents(

        'logs/gemini.log',

        "[" . date('Y-m-d H:i:s') . "] "
        . $message . PHP_EOL,

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

        if(

            mb_strlen($current . $paragraph)
            > $chunkSize

        ){

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
   GEMINI REQUEST
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

أنت نظام ذكي لتحليل الملفات والمستندات.

اسم الملف:
".$fileName."

المطلوب:

اذا كان النص المرسل باللغة العربية لخصه باللغه العربية اما اذا كان بالانجليزية لخصه للانجليزية مع شرح كل فقرة بالعربي
- حلل و لخص النص فقط للمفيد
- لا تخترع معلومات
- لا تضف أي معلومات خارج النص
- اكتب النص باللغة العربية بشكل صحيح (RTL)
- لا تعكس الكلمات أو الحروف
- تأكد من أن النص العربي يظهر بشكل طبيعي من اليمين لليسار
- إذا كانت المعلومة غير واضحة اكتب:
(المعلومة غير واضحة في الملف)

قواعد مهمة:

- ممنوع كتابة مقدمات عامة
- ممنوع استخدام معلومات خارجية
- اعتمد 100% على النص المرسل
- لا تتحدث عن الذكاء الاصطناعي إلا إذا كان موجودًا بالنص

أعد النتيجة HTML فقط باستخدام:

<div>
<h2>
<p>
<ul>
<li>
<table>
<tr>
<td>

استخدم تصميم حديث inline CSS.

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

            curl_setopt_array($ch,[

                CURLOPT_URL => $url,

                CURLOPT_RETURNTRANSFER => true,

                CURLOPT_POST => true,

                CURLOPT_CONNECTTIMEOUT => 15,

                CURLOPT_TIMEOUT => 120,

                CURLOPT_SSL_VERIFYPEER => true,

                CURLOPT_SSL_VERIFYHOST => 2,

                CURLOPT_HTTPHEADER => [

                    "Content-Type: application/json"
                ],

                CURLOPT_POSTFIELDS => json_encode($data)
            ]);

            $response = curl_exec($ch);

            $curlError = curl_error($ch);

            curl_close($ch);

            if($curlError){

                writeLog(
                    "CURL ERROR [".$model."]: "
                    .$curlError
                );

                sleep($attempt * 2);

                continue;
            }

            writeLog(

                "[".$model."] ".
                substr($response,0,2000)
            );

            $result = json_decode($response, true);

            $errorMessage =
            $result['error']['message']
            ?? '';

            if(

                str_contains($errorMessage,'429')
                ||
                str_contains($errorMessage,'quota')
                ||
                str_contains($errorMessage,'overloaded')
                ||
                str_contains($errorMessage,'high demand')
            ){

                sleep($attempt * 3);

                continue;
            }

            if(isset($result['error'])){

                writeLog(
                    "API ERROR [".$model."]: "
                    .$errorMessage
                );

                break;
            }

            $text =
            $result['candidates'][0]['content']['parts'][0]['text']
            ?? '';

            if(empty($text)){

                sleep($attempt * 2);

                continue;
            }

            $text = preg_replace(
                '/^```html\s*/i',
                '',
                $text
            );

            $text = preg_replace(
                '/```$/',
                '',
                $text
            );

            $text = preg_replace(
                '#<script(.*?)>(.*?)</script>#is',
                '',
                $text
            );

            $text = preg_replace(
                '#<iframe(.*?)>(.*?)</iframe>#is',
                '',
                $text
            );

            $text = preg_replace(
                '#<form(.*?)>(.*?)</form>#is',
                '',
                $text
            );

            return [

                'success' => true,

                'text' => trim($text)
            ];
        }
    }

    return [

        'success' => false,

        'message' =>
        'فشل الاتصال بـ Gemini'
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
   CACHE
===================================================== */

$ai_output = '';

if(!empty($file['ai_summary'])){

    $ai_output = $file['ai_summary'];

}else{

    /* =====================================================
       FILE CHECK
    ===================================================== */

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

    try{

        $pdf_text = '';

        /* PDF */

        if($mime == 'application/pdf'){

            $parser = new Parser();

            $pdf = $parser->parseFile($file_path);

            $pdf_text = $pdf->getText();
        }

        /* DOCX */

        elseif(

            $mime ==
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'

        ){

            $phpWord = IOFactory::load($file_path);

            foreach($phpWord->getSections() as $section){

                $elements = $section->getElements();

                foreach($elements as $element){

                    if(method_exists($element,'getText')){

                        $pdf_text .=
                        $element->getText()
                        . "\n";
                    }
                }
            }
        }

        /* DOC */

        elseif($mime == 'application/msword'){

            $content = file_get_contents($file_path);

            $content = strip_tags($content);

            $pdf_text = $content;
        }

        /* TXT */

        elseif($mime == 'text/plain'){

            $pdf_text = file_get_contents($file_path);

            $pdf_text = mb_convert_encoding(

                $pdf_text,

                'UTF-8',

                'UTF-8, ISO-8859-1, Windows-1256'
            );
        }

    }catch(Exception $e){

        die(
            'فشل قراءة الملف: '
            . $e->getMessage()
        );
    }

    if(empty(trim($pdf_text))){

        die('تعذر استخراج النص من الملف');
    }

    /* =====================================================
       CLEAN
    ===================================================== */

    $pdf_text = cleanFileText($pdf_text);

    $pdf_text = mb_substr($pdf_text, 0, 15000);

    /* =====================================================
       CHUNKS
    ===================================================== */

    $chunks = splitTextIntoChunks(

        $pdf_text,

        4000
    );

    $final_summary = '';

    /* =====================================================
       LOOP
    ===================================================== */

    foreach($chunks as $index => $chunk){

        sleep(2);

        $result = callGemini(

            $chunk,

            $apiKey,

            $file['file_name']
        );

        if($result['success']){

            $final_summary .=
            $result['text']
            . "\n";
        }

        else{

            $final_summary .= "

            <div style='
                background:#fff0f0;
                color:#b30000;
                padding:20px;
                border-radius:15px;
                margin-bottom:20px;
            '>

            فشل تحليل الجزء رقم "
            .($index+1).
            "

            <br><br>

            ".$result['message']."

            </div>
            ";
        }
    }

    /* =====================================================
       FINAL HTML
    ===================================================== */

    $ai_output = '

    <!DOCTYPE html>

    <html lang="ar" dir="rtl">

    <head>

    <meta charset="UTF-8">

    <style>

    body{

        font-family:Tahoma;

        background:#f4f7fb;

        padding:30px;

        line-height:1.9;
    }

    table{

        width:100%;

        border-collapse:collapse;

        margin-top:20px;
    }

    td,th{

        border:1px solid #ddd;

        padding:12px;
    }

    h2{

        color:#1e3a8a;
    }

    div{

        background:#fff;

        padding:25px;

        margin-bottom:25px;

        border-radius:20px;

        box-shadow:0 5px 20px rgba(0,0,0,.08);
    }

    </style>

    </head>

    <body>

    '.$final_summary.'

    </body>

    </html>
    ';

    /* =====================================================
       SAVE CACHE
    ===================================================== */

    $update = $conn->prepare("
        UPDATE upload
        SET ai_summary = ?
        WHERE id = ?
    ");

    $update->execute([

        $ai_output,

        $file_id
    ]);
}

/* =====================================================
   DOWNLOAD PDF
===================================================== */

if(isset($_GET['download_summary'])){

    $mpdf = new Mpdf([

        'mode' => 'utf-8',

        'format' => 'A4',

        'default_font' => 'dejavusans'
    ]);

    $mpdf->WriteHTML($ai_output);

    $mpdf->Output(

        'summary.pdf',

        'D'
    );

    exit();
}

?>
<!DOCTYPE html>
<html lang="ar">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تلخيص" data-en="summarize"></title>
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>
<style>
.ai-summary-container{

    width:100%;

    background:#fff;

    border-radius:20px;

    padding:20px;

    margin-top:25px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.07);
}
.summary-actions{

    margin-top:40px;

    padding-top:20px;

    display:flex;

    gap:40px;

    flex-wrap:wrap;

    justify-content:center;
}

.summary-actions a,
.summary-actions button{

    margin:8px;
}

iframe{

    width:100%;

    height:900px;

    border:none;

    border-radius:15px;

    background:#fff;
}

</style>

</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="playlist">

<h1 class="heading">

تلخيص الملف

</h1>

<div class="row">

<div class="col">

<div class="details">

<h2>

<?= htmlspecialchars($file['file_name']); ?>

</h2>

<div class="ai-summary-container">

<iframe
srcdoc="<?= htmlspecialchars($ai_output, ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>">
</iframe>

</div>

<div class="summary-actions">

<a
href="upload.php"
class="inline-btn">

رجوع

</a>

<a
href="<?= htmlspecialchars($file['file_path']); ?>"
target="_blank"
class="inline-btn">

قراءة الملف

</a>

<a
href="?file_id=<?= $file_id; ?>&download_summary=1"
class="inline-option-btn">

تحميل PDF

</a>

<a
href="questions.php?file_id=<?= $file_id; ?>"
class="inline-btn">

الأسئلة

</a>

</div>

</div>

</div>

</div>

</section>

<script src="js/script.js"></script>

<script src="js/switcher.js"></script>

</body>

</html>

