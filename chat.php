<?php
include 'components/connect.php';

date_default_timezone_set('Asia/Riyadh');
/* المستخدم الحالي */

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
   exit;
}

/* بيانات المحادثة */

$type = $_GET['receiver_type'] ?? '';
$receiver_id = $_GET['receiver_id'] ?? '';

/* بيانات الطرف الثاني */

if($type == 'admin'){

   $receiver_type = 'admin';

   $chat_name = 'الدعم الفني';
   $chat_icon = '👑';

}else{

   $receiver_type = 'teacher';

   $get_teacher = $conn->prepare("
      SELECT name, image
      FROM tutors
      WHERE id = ?
   ");

   $get_teacher->execute([$receiver_id]);

   $teacher = $get_teacher->fetch(PDO::FETCH_ASSOC);
$teacher_image = $teacher['image'] ?? 'default.png';
   $chat_name = $teacher['name'] ?? 'المعلم';
   $chat_icon = '';
}

/* إرسال رسالة */

if(isset($_POST['send'])){

   $msg = trim($_POST['message']);

   if(!empty($msg)){

      $msg = htmlspecialchars($msg);

      $insert = $conn->prepare("
         INSERT INTO messages
         (
            sender_id,
            sender_type,
            receiver_id,
            receiver_type,
            message
         )
         VALUES (?,?,?,?,?)
      ");

      $insert->execute([
         $user_id,
         'student',
         $receiver_id,
         $receiver_type,
         $msg
      ]);

      header("location:?receiver_type=$type&receiver_id=$receiver_id");
      exit;
   }
}

/* جلب الرسائل */

$select = $conn->prepare("
SELECT *
FROM messages

WHERE

(
   sender_id = ?
   AND sender_type = 'student'
   AND receiver_id = ?
   AND receiver_type = ?
)

OR

(
   sender_id = ?
   AND sender_type = ?
   AND receiver_id = ?
   AND receiver_type = 'student'
)

ORDER BY created_at ASC
");

$select->execute([

   $user_id,
   $receiver_id,
   $receiver_type,

   $receiver_id,
   $receiver_type,
   $user_id

]);
function getChatDateLabel($date){

   $msgDay = date('Y-m-d', strtotime($date));
   $today = date('Y-m-d');
   $yesterday = date('Y-m-d', strtotime('-1 day'));

   if($msgDay == $today){
      return 'اليوم';
   }

   if($msgDay == $yesterday){
      return 'أمس';
   }

   $days = [
      'Sunday'    => 'الأحد',
      'Monday'    => 'الإثنين',
      'Tuesday'   => 'الثلاثاء',
      'Wednesday' => 'الأربعاء',
      'Thursday'  => 'الخميس',
      'Friday'    => 'الجمعة',
      'Saturday'  => 'السبت'
   ];

   if(strtotime($date) >= strtotime('-7 days')){
      return $days[date('l', strtotime($date))];
   }

   return date('d/m/Y', strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
<title>المحادثة</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap">

<link rel="stylesheet" href="css/all.min.css">

<link id="style" rel="stylesheet" href="css/style-ar.css">

<style>
   .avatar img{
   width:100%;
   height:100%;
   object-fit:cover;
   border-radius:50%;
}

body.g-body{

   margin:0;
   padding:0;

   font-family:'Cairo',sans-serif;

   
   background-color: var(--light-bg);
   color:#fff;
}

body.g-body.dark{
--white:#222;
--black:#fff;
--light-color:#aaa;
--light-bg:#333;
--border:.1rem solid rgba(255,255,255,.2);
}
/* الحاوية */
.chat-date {
    align-self: center;

    background: rgba(255, 255, 255, 0.15); /* خلفية شفافة */
    color: #000000;

    padding: 6px 14px;
    border-radius: 8px;

    font-size: 12px;
    margin: 10px 0;

    border: 1px solid rgba(255, 255, 255, 0.2);

    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);

    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);

    position: sticky;
}
.chat-box{

   width:100%;
   height:calc(100vh - 80px);

   display:flex;
   flex-direction:column;

   background:rgba(11,20,26,.72);

   backdrop-filter:blur(16px);

   border-top:1px solid rgba(255,255,255,.05);

   overflow:hidden;
}

/* الهيدر */

.header_title{

   height:72px;

   display:flex;
   align-items:center;
   gap:14px;

   padding:0 18px;

   background: #5a859c ;

   border-bottom:1px solid rgba(255,255,255,.05);

   backdrop-filter:blur(12px);
}
body.dark .header_title{
     background: #163f52 !important;
}
/* الرجوع */

.back{

   color:#fff;

   font-size:22px;

   text-decoration:none;

   transition:.25s;
}

.back:hover{

   color:#00d4aa;
}

/* الصورة */

.avatar{

   width:48px;
   height:48px;

   border-radius:50%;

   display:flex;
   align-items:center;
   justify-content:center;

   background:
   linear-gradient(
      135deg,
      #00a884,
      #00d4aa
   );

   font-size:22px;

   position:relative;
}

/* الأونلاين */

.online{

   width:12px;
   height:12px;

   border-radius:50%;

   background:#00ff6a;

   

   position:absolute;

   bottom:0;
   left:0;
}

/* المعلومات */

.info{
   flex:1;
}

.name{

   font-size:16px;
   font-weight:700;
}

.status{

   font-size:12px;

   color:#8fa3ad;
}

/* الرسائل */

.messages{

   flex:1;

   overflow-y:auto;

   padding:20px;

   display:flex;
   flex-direction:column;

   gap:12px;

   scroll-behavior:smooth;
}

/* الرسالة */

.msg{

   position:relative;

   max-width:75%;

   padding:12px 28px 24px;

   border-radius:18px;

   line-height:1.7;

   font-size:15px;

   word-break:break-word;

   animation:fade .2s ease;
}

/* رسائلي */

.me{

   align-self:flex-end;

   background:
   linear-gradient(
      135deg,
      rgba(0,168,132,.45),
      rgba(0,92,75,.95)
   );

   border-top-right-radius:5px;

   box-shadow:
   0 0 18px rgba(0,168,132,.18);
}

/* رسائله */

.you{

   align-self:flex-start;

   background:
   linear-gradient(
      135deg,
      rgba(42,57,66,.95),
      rgba(32,44,51,.8)
   );

   border-top-left-radius:5px;
}

/* الوقت */

.time{

   position:absolute;

   bottom:6px;
   left:12px;

   font-size:11px;

   color:#d1d7db;

   opacity:.7;
}

/*صندوق الإرسال */

.send-box{

   padding:14px;

   display:flex;
   align-items:center;

   gap:10px;

   background: #5a859c;

   border-top:1px solid rgba(255,255,255,.05);

   backdrop-filter:blur(12px);
}
body.dark .send-box{
     background: #163f52 !important;
}

/* الإدخال */

.input{

   flex:1;

   height:54px;

   border:none;
   outline:none;

   border-radius:30px;

   padding:0 20px;

   font-size:15px;

   color:#fff;

   background:rgba(42,57,66,.75);

   border:1px solid rgba(255,255,255,.06);

   transition:.25s;
}

.input::placeholder{
   color:#8fa3ad;
}

.input:focus{

   border-color:#00a884;

   box-shadow:
   0 0 18px rgba(0,168,132,.22);
}

/* زر الإرسال */

.btn_chat{

   width:54px;
   height:54px;

   border:none;

   border-radius:50%;

   cursor:pointer;

   color:#fff;

   font-size:18px;

   background:
   linear-gradient(
      135deg,
      #00a884,
      #00d4aa
   );

   transition:.25s;
}

.btn_chat:hover{

   transform:scale(1.05);
}

/* السكرول */

.messages::-webkit-scrollbar{
   width:5px;
}

.messages::-webkit-scrollbar-thumb{

   background: #00a884;

   border-radius:20px;
}
.messages{
background-color: var(--light-bg);

}
/* الأنيميشن */

@keyframes fade{

   from{
      opacity:0;
      transform:translateY(10px);
   }

   to{
      opacity:1;
      transform:translateY(0);
   }
}

/* الجوال */

@media(max-width:768px){

   .msg{
      max-width:88%;
      font-size:14px;
   }

   .messages{
      padding:14px;
   }

   .header_title{
      padding:0 12px;
   }
}
.msg{
   position:relative;
}
.delete-msg{
   position:absolute;
   top:6px;
   right:8px;

   background:none;      /* ❌ بدون خلفية */
   border:none;         /* ❌ بدون حدود */
   padding:0;

   color:rgba(255,255,255,0.6); /* لون خفيف */
   font-size:13px;

   cursor:pointer;

   opacity:0;
   transform:scale(0.8);

   transition:.2s ease;
}

/* يظهر عند المرور */
.msg:hover .delete-msg{
   opacity:1;
   transform:scale(1);
}

/* عند المرور على الزر نفسه */
.delete-msg:hover{
   color:#ff4d4d; /* يتحول أحمر فقط */
}
</style>

</head>

<body class="g-body">

<?php include 'components/user_header.php'; ?>

<div class="chat-box">

   <!-- الهيدر -->

   <div class="header_title">

      <a href="chats.php" class="back">
         <i class="fas fa-arrow-right"></i>
      </a>

      <div class="avatar">
   <img src="uploaded_files/<?= htmlspecialchars($teacher_image); ?>" alt="">
   <span class="online"></span>
</div>

      <div class="info">

         <div class="name">
            <?= $chat_name ?>
         </div>

         <div class="status">
            متصل الآن
         </div>

      </div>

   </div>

   <!-- الرسائل -->

   <div class="messages" id="messages">
<?php

$last_date = '';

while($m = $select->fetch(PDO::FETCH_ASSOC)){

    $current_date = date('Y-m-d', strtotime($m['created_at']));

    if($current_date != $last_date){
        echo '<div class="chat-date">'.getChatDateLabel($m['created_at']).'</div>';
        $last_date = $current_date;
    }

?>

<div class="msg <?= $m['sender_id'] == $user_id ? 'me' : 'you' ?>">

   <div class="text">
      <?= htmlspecialchars($m['message']) ?>
   </div>

   <span class="time">
      <?= date('h:i A', strtotime($m['created_at'])) ?>
   </span>
  <?php if($m['sender_id'] == $user_id){ ?>
   <button class="delete-msg" data-id="<?= $m['id']; ?>">
   <i class="fas fa-trash"></i>
</button>
<?php } ?>
</div>

<?php } ?>

   </div>

   <!-- الإرسال -->

   <form method="POST" class="send-box">

      <input
         type="text"
         name="message"
         class="input"
         placeholder="اكتب رسالة..."
         autocomplete="off"
         required
      >

      <button type="submit" class="btn_chat" name="send">
         <i class="fas fa-paper-plane"></i>
      </button>

   </form>

</div>

<script>

let messages = document.getElementById('messages');

messages.scrollTop = messages.scrollHeight;

document.querySelectorAll('.delete-msg').forEach(btn => {

   btn.addEventListener('click', function(){

      let msgId = this.getAttribute('data-id');
      let msgBox = this.closest('.msg');

      fetch('delete_message.php', {
         method: 'POST',
         headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
         },
         body: 'id=' + msgId
      })
      .then(res => res.text())
      .then(data => {

         if(data.trim() === 'success'){
            msgBox.remove(); // 👈 حذف من الشاشة بدون ريفرش
         }

      });

   });

});

</script>
<?php include 'components/footer.php'; ?>

<script src="js/script.js" defer></script>
<script src="js/switcher.js" defer></script>
</body>
</html>