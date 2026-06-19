<?php
include 'components/connect.php';

$user_id = $_COOKIE['user_id'] ?? '';

if($user_id == ''){
   header('location:login.php');
   exit();
}

if(!isset($_GET['playlist_id'])){
   header('location:courses.php');
   exit();
}

$playlist_id = $_GET['playlist_id'];

$stmt = $conn->prepare("SELECT * FROM playlist WHERE id = ? AND status = ?");
$stmt->execute([$playlist_id, 'active']);

if($stmt->rowCount() == 0){
   header('location:courses.php');
   exit();
}

$playlist = $stmt->fetch(PDO::FETCH_ASSOC);

$q_stmt = $conn->prepare("SELECT COUNT(*) as total FROM playlist_questions WHERE playlist_id = ?");
$q_stmt->execute([$playlist_id]);
$q_count = $q_stmt->fetch(PDO::FETCH_ASSOC)['total'];

if($q_count == 0){
   header("location:playlist.php?get_id=$playlist_id");
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Start Quiz</title>

<link rel="stylesheet" href="css/style-ar.css">
<link rel="stylesheet" href="css/all.min.css">
<link id="style" rel="stylesheet" href="css/style-ar.css">
<style>
/* الصفحة */
.quiz-start {
    display: flex;
    align-items: center;
    justify-content: space-around;
    min-height: 90vh;
    padding: 60px;
    flex-wrap: wrap;
    gap: 30px; /* مسافة بين الصورة والنص */
}

/* المعلم */
.teacher {
    width: 320px;       /* حجم افتراضي للكمبيوتر */
    max-width: 90%;     /* لا يتجاوز عرض الشاشة */
    animation: float 3s ease-in-out infinite;
}

/* حركة المعلم */
@keyframes float {
    0% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
    100% { transform: translateY(0); }
}

/* النصوص */
.quiz-text {
    max-width: 600px;
    font-size: 1.2rem;
    text-align: left;
}

/* كتابة متحركة */
.typing {
    font-size: 28px;
    font-weight: bold;
    border-right: 3px solid;
    white-space: nowrap;
    overflow: hidden;
    width: 0;
    animation: typing 3s steps(30) forwards, blink 0.7s infinite;
}

@keyframes typing {
    to { width: 100%; }
}
@keyframes blink {
    50% { border-color: transparent; }
}

/* التفاصيل */
.quiz-info {
    margin-top: 25px;
    color:currentColor;
    font-size: 18px;
}

/* زر البداية */
.start-btn {
    display: inline-block;
    margin-top: 30px;
    padding: 16px 35px;
    font-size: 20px;
    background: #6c5ce7;
    color: #fff;
    border-radius: 30px;
    text-decoration: none;
    transition: 0.3s;
}
.start-btn:hover {
    background: #4834d4;
    transform: scale(1.1);
}

/* Credit Storyset */
.credit {
    margin-top: 20px;
    font-size: 14px;
    text-align: center;
}
.credit a {
    color: #aaa;
    text-decoration: none;
}
.credit a:hover {
    text-decoration: underline;
}

/* ========== Media Queries ========== */

/* تابلت */
@media (max-width: 1024px) {
    .quiz-start {
        flex-direction: column;
        padding: 40px 20px;
    }
    .teacher {
        width: 280px;
    }
    .quiz-text {
        max-width: 90%;
        font-size: 1.1rem;
        text-align: center;
    }
    .typing {
        font-size: 24px;
    }
    .quiz-info {
        font-size: 16px;
    }
    .start-btn {
        font-size: 18px;
        padding: 14px 30px;
    }
}

/* موبايل */
@media (max-width: 600px) {
    .teacher {
        width: 220px;
        margin-bottom: 20px;
    }
    .quiz-text {
        font-size: 1rem;
        text-align: center;
    }
    .typing {
        font-size: 20px;
    }
    .quiz-info {
        font-size: 14px;
    }
    .start-btn {
        font-size: 16px;
        padding: 12px 25px;
    }
}
</style>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="quiz-start">

   <!-- المعلم -->
  
<img src="images/Teacher-pana.png" class="teacher" alt="Teacher">

   <!-- النصوص -->
   <div class="quiz-text">

      <!-- typing effect -->
      <div class="typing"
           data-ar="هل أنت مستعد لاختبار مهاراتك؟"
           data-en="Are you ready to test your skills?">
           هل أنت مستعد لاختبار مهاراتك؟
      </div>

      <div class="quiz-info">

         <p data-ar="عدد الأسئلة: <?= $q_count; ?>"
            data-en="Total Questions: <?= $q_count; ?>">
            عدد الأسئلة: <?= $q_count; ?>
         </p>

         <p data-ar="لكل سؤال 7 ثواني فقط!"
            data-en="You have 7 seconds per question!">
            لكل سؤال 7 ثواني فقط!
         </p>

      </div>

      <!-- زر البداية -->
      <a href="playlist_quiz.php?playlist_id=<?= $playlist_id; ?>" class="start-btn"
         data-ar="لنبدأ الآن 🚀"
         data-en="Let's Start 🚀">
         لنبدأ الآن 🚀
      </a>

      <!-- Credit Storyset -->
      <p class="credit">
        <!-- المعلم -->
      \

   </div>

</section>

<script>
    
// دعم اللغة
document.querySelectorAll('[data-ar]').forEach(el => {
   let lang = localStorage.getItem("lang") || "ar";
   el.innerText = el.getAttribute("data-" + lang);
});
</script>

<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>