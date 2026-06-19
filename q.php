<?php
include 'components/connect.php';

$user_id = $_COOKIE['user_id'] ?? '';
if(!$user_id){
   header('location:login.php');
   exit;
}

/* =========================
   معالجة الحفظ (مرة واحدة فقط)
========================= */
if(isset($_GET['score'], $_GET['total'], $_GET['playlist_id']) && !isset($_GET['done'])){

   $score = (int)$_GET['score'];
   $total = (int)$_GET['total'];
   $playlist_id = (int)$_GET['playlist_id'];

   $percentage = ($total > 0) ? round(($score/$total)*100) : 0;
   $passed = $percentage >= 50 ? 1 : 0;

   // منع التكرار
   $check = $conn->prepare("SELECT id FROM quiz_certificates WHERE student_id=? AND playlist_id=?");
   $check->execute([$user_id, $playlist_id]);

   if(!$check->fetch()){
      $conn->prepare("INSERT INTO quiz_certificates (student_id, playlist_id, score, passed) VALUES (?,?,?,?)")
           ->execute([$user_id, $playlist_id, $percentage, $passed]);
   }

   // 🔥 إعادة توجيه لمنع الريفريش
   header("Location: quiz_result.php?done=1&playlist_id=$playlist_id");
   exit;
}

/* =========================
   عرض البيانات
========================= */
if(!isset($_GET['playlist_id'])){
   header('location:courses.php');
   exit;
}

$playlist_id = (int)$_GET['playlist_id'];

/* نجيب آخر نتيجة */
$stmt = $conn->prepare("SELECT score FROM quiz_certificates WHERE student_id=? AND playlist_id=? ORDER BY id DESC LIMIT 1");
$stmt->execute([$user_id, $playlist_id]);
$percentage = (int)$stmt->fetchColumn();

/* التقدير */
$grade_ar = $percentage >= 90 ? "ممتاز" :
            ($percentage >= 75 ? "جيد جدًا" :
            ($percentage >= 50 ? "جيد":"ضعيف"));

/* اسم المستخدم */
$stmt_user = $conn->prepare("SELECT name FROM users WHERE id=?");
$stmt_user->execute([$user_id]);
$user_name = $stmt_user->fetchColumn() ?: $user_id;

/* اسم الكورس */
$stmt_playlist = $conn->prepare("SELECT title FROM playlist WHERE id=?");
$stmt_playlist->execute([$playlist_id]);
$playlist_name = $stmt_playlist->fetchColumn() ?: "---";

/* التاريخ */
$certificate_date = date("d / m / Y");
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>Quiz</title>

<style>
body {
    font-family: 'Cairo', sans-serif;
    background: linear-gradient(135deg, #6c5ce7, #00cec9);
    margin: 0;
}

.quiz-container {
    max-width: 650px;
    margin: 60px auto;
    background: #fff;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    animation: fadeIn 0.5s ease;
}

.question {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}

.options button {
    width: 100%;
    padding: 15px;
    margin-bottom: 12px;
    font-size: 18px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    background: #f1f2f6;
    transition: 0.3s;
}

.options button:hover {
    transform: scale(1.03);
}

.correct {
    background: #00b894 !important;
    color: #fff;
}

.wrong {
    background: #d63031 !important;
    color: #fff;
}

.timer {
    margin-bottom: 10px;
}

.progress-bar {
    width: 100%;
    height: 10px;
    background: #ddd;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
}

.progress-fill {
    height: 100%;
    width: 0%;
    background: #6c5ce7;
    transition: width 0.4s;
}

.progress {
    text-align: center;
    margin-top: 10px;
}

@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}

@media(max-width:600px){
    .quiz-container {
        margin: 20px;
        padding: 20px;
    }
}
</style>

</head>

<body>

<div class="quiz-container">

    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <div class="timer">
        الوقت المتبقي: <span id="timer">7</span>
    </div>

    <div class="question" id="question"></div>

    <div class="options" id="options"></div>

    <div class="progress" id="progress"></div>

</div>

<script>
const questions = <?= json_encode($questions); ?>;

let currentIndex = 0;
let score = 0;
let timer = 7;
let timerInterval;

function showQuestion(index){
    const q = questions[index];

    document.getElementById('question').innerText = q.question;

    const optionsDiv = document.getElementById('options');
    optionsDiv.innerHTML = '';

    ['option1','option2','option3','option4'].forEach(opt=>{
        const btn = document.createElement('button');
        btn.innerText = q[opt];

        btn.onclick = () => {
            clearInterval(timerInterval);

            if(q[opt] === q.answer){
                btn.classList.add('correct');
                score++;
            } else {
                btn.classList.add('wrong');
            }

            // إظهار الإجابة الصحيحة
            [...optionsDiv.children].forEach(b=>{
                if(b.innerText === q.answer){
                    b.classList.add('correct');
                }
                b.disabled = true;
            });

            setTimeout(nextQuestion, 1000);
        };

        optionsDiv.appendChild(btn);
    });

    document.getElementById('progress').innerText =
        `السؤال ${index+1} من ${questions.length}`;

    // تحديث progress bar
    let percent = (index / questions.length) * 100;
    document.getElementById('progressFill').style.width = percent + "%";

    // إعادة ضبط التايمر
    timer = 7;
    document.getElementById('timer').innerText = timer;
    document.getElementById('timer').style.color = '#000';

    clearInterval(timerInterval);
    timerInterval = setInterval(()=>{
        timer--;
        document.getElementById('timer').innerText = timer;

        if(timer <= 3){
            document.getElementById('timer').style.color = 'red';
        }

        if(timer <= 0){
            clearInterval(timerInterval);
            nextQuestion();
        }
    },1000);
}

function nextQuestion(){
    currentIndex++;

    if(currentIndex < questions.length){
        showQuestion(currentIndex);
    } else {
        window.location.href =
        `quiz_result.php?score=${score}&total=${questions.length}&playlist_id=${<?= $playlist_id; ?>}`;
    }
}

// بدء الكويز
showQuestion(currentIndex);
</script>

</body>
</html>