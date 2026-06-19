<?php
include 'components/connect.php';

/* ================= تسجيل الدخول ================= */
$user_id = $_COOKIE['user_id'] ?? '';
if(!$user_id){
    header('location:login.php');
    exit();
}

/* ================= playlist_id ================= */
$playlist_id = $_GET['playlist_id'] ?? '';
if(!$playlist_id){
    header('location:courses.php');
    exit();
}

/* ================= منع إعادة الاختبار بعد الإكمال ================= */
$total_q = $conn->prepare("SELECT COUNT(*) FROM playlist_questions WHERE playlist_id = ?");
$total_q->execute([$playlist_id]);
$total_questions = $total_q->fetchColumn();

$answered_q = $conn->prepare("
    SELECT COUNT(*) 
    FROM quiz_answers qa
    JOIN playlist_questions pq ON qa.question_id = pq.id
    WHERE qa.student_id = ? AND pq.playlist_id = ?
");
$answered_q->execute([$user_id, $playlist_id]);
$answered_count = $answered_q->fetchColumn();

if($answered_count >= $total_questions && $total_questions > 0){
    header("location:quiz_result.php?playlist_id=$playlist_id");
    exit();
}

/* ================= جلب الأسئلة ================= */
$stmt = $conn->prepare("
    SELECT * 
    FROM playlist_questions 
    WHERE playlist_id = ? 
    ORDER BY id ASC
");
$stmt->execute([$playlist_id]);

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if(!$questions){
    header("location:courses.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>Quiz</title>

<style>
body {
    font-family: Arial;
    background: linear-gradient(135deg,#6c5ce7,#00cec9);
    margin: 0;
}

.quiz-container {
    max-width: 650px;
    margin: 60px auto;
    background: #fff;
    padding: 25px;
    border-radius: 15px;
}

.question {
    font-size: 22px;
    margin-bottom: 20px;
}

.options button {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    background: #f1f1f1;
}

.correct { background: green !important; color: #fff; }
.wrong { background: red !important; color: #fff; }

.timer { margin-bottom: 10px; }
.progress-bar { height: 10px; background: #ddd; border-radius: 10px; }
.progress-fill { height: 10px; width: 0%; background: #6c5ce7; }
</style>
</head>

<body>

<div class="quiz-container">

    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>

    <div class="timer">الوقت: <span id="timer">7</span></div>

    <div class="question" id="question"></div>
    <div class="options" id="options"></div>

</div>

<script>
const questions = <?= json_encode($questions, JSON_UNESCAPED_UNICODE); ?>;
const playlist_id = "<?= $playlist_id ?>";

let index = 0;
let score = 0;
let timer = 7;
let interval;

/* ================= عرض السؤال ================= */
function showQuestion() {

    let q = questions[index];

    document.getElementById('question').innerText = q.question;//عرض نص السؤال

    let optionsDiv = document.getElementById('options');
    optionsDiv.innerHTML = '';

    ['option1','option2','option3','option4'].forEach(opt => {//إنشاء الخيارات (الأزرار)

        let btn = document.createElement('button');
        btn.innerText = q[opt];

        btn.onclick = function () {//لما المستخدم يضغط على خيار

            clearInterval(interval); //يوقف المؤقت

            let isCorrect = 0;

            /* ✅ التصحيح هنا */
            if (opt === q.answer) {
                btn.classList.add('correct');
                score++;
                isCorrect = 1;
            } else {
                btn.classList.add('wrong');
            }

            /* حفظ الإجابة */
            fetch('save_answer.php', {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({
                    question_id: q.id,
                    selected_option_id: opt.replace('option',''),
                    playlist_id: playlist_id
                })
            });

            /* عرض الصحيح */
            [...optionsDiv.children].forEach((b,i)=>{
                if('option'+(i+1) === q.answer){
                    b.classList.add('correct');
                }
                b.disabled = true;
            });

            setTimeout(nextQuestion, 1000);
        };

        optionsDiv.appendChild(btn);
    });

    /* progress */
    document.getElementById('progressFill').style.width =
        ((index+1)/questions.length)*100 + "%";

    /* timer */
    timer = 7;
    document.getElementById('timer').innerText = timer;

    clearInterval(interval);

    interval = setInterval(()=>{
        timer--;
        document.getElementById('timer').innerText = timer;

        if(timer <= 0){
            clearInterval(interval);

            fetch('save_answer.php', {
                method:'POST',
                headers:{'Content-Type':'application/json'},
                body: JSON.stringify({
                    question_id: q.id,
                    selected_option_id: null,
                    playlist_id: playlist_id
                })
            });

            nextQuestion();
        }

    },1000);
}

/* ================= التالي ================= */
function nextQuestion(){
    index++;

    if(index < questions.length){
        showQuestion();
    } else {
        window.location.href =
        "quiz_result.php?playlist_id="+playlist_id+"&score="+score+"&total="+questions.length;
    }
}

showQuestion();

</script>

</body>
</html>