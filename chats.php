<?php

include 'components/connect.php';

/* تسجيل الدخول */

if(isset($_COOKIE['user_id'])){

   $user_id = $_COOKIE['user_id'];

}else{

   $user_id = '';

}

/* المعلمين */
$teachers = $conn->prepare("
SELECT DISTINCT t.*
FROM tutors t
WHERE t.id IN (

   SELECT receiver_id
   FROM messages
   WHERE sender_id = ?
   AND sender_type = 'student'
   AND receiver_type = 'teacher'

   UNION

   SELECT sender_id
   FROM messages
   WHERE receiver_id = ?
   AND receiver_type = 'student'
   AND sender_type = 'teacher'
)
AND t.is_active = 1
");
$teachers->execute([$user_id, $user_id]);
?>

<!DOCTYPE html>
<html lang="ar">

<head>

<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>الدردشات</title>

<link rel="preconnect" href="https://fonts.googleapis.com">

<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">


   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">
<link rel="stylesheet" href="css/login-overlay.css">

<style>

*{
   margin:0;
   padding:0;
   box-sizing:border-box;
}

body{
   font-family:'Cairo', sans-serif;
  
   color:#fff;
   overflow-x:hidden;
}

.header_chat{
   background: #5a859c ;
   padding:15px 20px;
   display:flex;
   align-items:center;
   justify-content:space-between;
   position:sticky;
   top:0;
   z-index:1000;
}

body.dark .header_chat{
     background: #163f52 !important;
}
.chat-list1{
   padding:10px;
}
.chat{
   display:flex;
   align-items:center;
   gap:14px;

   background:rgba(59, 130, 246, 0.12);
   backdrop-filter:blur(18px);
   -webkit-backdrop-filter:blur(18px);

   border:1px solid rgba(96, 165, 250, 0.18);
   border-radius:18px;

   padding:14px;
   margin-bottom:12px;

   color:#fff;
   text-decoration:none;

   box-shadow:
      0 4px 20px rgba(59,130,246,.08),
      inset 0 1px 1px rgba(255,255,255,.08);

   transition:
      transform .3s ease,
      background .3s ease,
      box-shadow .3s ease,
      border-color .3s ease;
}

.chat:hover{
   transform:translateY(-4px) scale(1.03);

   background:rgba(59,130,246,.18);

   border-color:rgba(147,197,253,.3);

   box-shadow:
      0 10px 30px rgba(59,130,246,.18),
      0 0 20px rgba(59,130,246,.12);
}

.chat:active{
   transform:scale(.98);
}

.avatar{
   width:58px;
   height:58px;
   border-radius:50%;
   overflow:hidden;
   flex-shrink:0;
   position:relative;
}

.avatar img{
   width:100%;
   height:100%;
   object-fit:cover;
}

.online{
   width:14px;
   height:14px;
   background:#00ff6a;
   border-radius:50%;
   border:2px solid #202c33;
   position:absolute;
   bottom:2px;
   left:2px;
}

.chat-info{
   flex:1;
   overflow:hidden;
}

.top{
   display:flex;
   justify-content:space-between;
   margin-bottom:5px;
}

.name{
   font-size:17px;
   font-weight:700;
}

.time{
   font-size:12px;
   color:#8696a0;
}

.bottom{
   display:flex;
   justify-content:space-between;
   align-items:center;
}

.sub{
   font-size:13px;
   color:#8696a0;
   white-space:nowrap;
   overflow:hidden;
   text-overflow:ellipsis;
   max-width:220px;
}

.badge{
   background:#00a884;
   color:#fff;
   min-width:22px;
   height:22px;
   border-radius:50%;
   display:flex;
   align-items:center;
   justify-content:center;
   font-size:11px;
   font-weight:bold;
   padding:0 6px;
}

</style>

</head>

<body>

<?php include 'components/user_header.php'; ?>

<div class="header_chat">

   <h2>الدردشات</h2>

   <div>💬</div>

</div>

<div class="chat-list1">

<!-- الدعم الفني -->

<?php

/* آخر رسالة مع الأدمن */

$last_admin_msg = $conn->prepare("
SELECT * FROM messages

WHERE

(
sender_id = ?
AND sender_type = 'student'
AND receiver_type = 'admin'
)

OR

(
receiver_id = ?
AND receiver_type = 'student'
AND sender_type = 'admin'
)

ORDER BY id DESC LIMIT 1
");

$last_admin_msg->execute([$user_id, $user_id]);

$admin_msg = $last_admin_msg->fetch(PDO::FETCH_ASSOC);

$admin_text = 'مرحباً 👋 كيف يمكننا مساعدتك؟';

if($admin_msg){

   $admin_text = $admin_msg['message'];

}

/* عدد الرسائل غير المقروءة */

$admin_unseen = $conn->prepare("
SELECT * FROM messages

WHERE

receiver_id = ?
AND receiver_type = 'student'
AND sender_type = 'admin'
AND is_seen = 0
");

$admin_unseen->execute([$user_id]);

?>

<a class="chat" href="chat.php?receiver_type=admin&receiver_id=1">

   <div class="avatar">

      <img src="images/logo-1.png" alt="">
      <span class="online"></span>

   </div>

   <div class="chat-info">

      <div class="top">

         <div class="name">الدعم الفني</div>

         <div class="time">
            الآن
         </div>

      </div>

      <div class="bottom">

         <div class="sub">
            <?= $admin_text; ?>
         </div>

         <?php if($admin_unseen->rowCount() > 0){ ?>

            <div class="badge">
               <?= $admin_unseen->rowCount(); ?>
            </div>

         <?php } ?>

      </div>

   </div>

</a>

<!-- المدرسين -->

<?php while($teacher = $teachers->fetch(PDO::FETCH_ASSOC)){ ?>

<?php

/* آخر رسالة */

$last_message = $conn->prepare("
SELECT * FROM messages

WHERE

(
sender_id = ?
AND sender_type = 'student'
AND receiver_id = ?
AND receiver_type = 'teacher'
)

OR

(
sender_id = ?
AND sender_type = 'teacher'
AND receiver_id = ?
AND receiver_type = 'student'
)

ORDER BY id DESC LIMIT 1
");

$last_message->execute([
   $user_id,
   $teacher['id'],
   $teacher['id'],
   $user_id
]);

$message = $last_message->fetch(PDO::FETCH_ASSOC);

$last_text = 'اضغط لبدء المحادثة...';

if($message){

   $last_text = $message['message'];

}
$time_text = '';

if($message){

   $msg_date = strtotime($message['created_at']);

   if(date('Y-m-d', $msg_date) == date('Y-m-d')){

      $time_text = date('h:i A', $msg_date);

   }elseif(date('Y-m-d', $msg_date) == date('Y-m-d', strtotime('-1 day'))){

      $time_text = 'أمس';

   }else{

      $time_text = date('Y/m/d', $msg_date);

   }

}
/* الرسائل غير المقروءة */

$unseen = $conn->prepare("
SELECT * FROM messages

WHERE

receiver_id = ?
AND receiver_type = 'student'

AND sender_id = ?
AND sender_type = 'teacher'

AND is_seen = 0
");

$unseen->execute([
   $user_id,
   $teacher['id']
]);

?>

<a class="chat" href="chat.php?receiver_type=teacher&receiver_id=<?= $teacher['id']; ?>">

   <div class="avatar">

      <img src="uploaded_files/<?= $teacher['image']; ?>" alt="">

      <span class="online"></span>

   </div>

   <div class="chat-info">

      <div class="top">

         <div class="name">
            <?= $teacher['name']; ?>
         </div>


          <div class="time">
               <?= $time_text; ?>
            </div>
      </div>

      <div class="bottom">

         <div class="sub">
            <?= $last_text; ?>
         </div>

         <?php if($unseen->rowCount() > 0){ ?>

            <div class="badge">
               <?= $unseen->rowCount(); ?>
            </div>

         <?php } ?>

      </div>

   </div>

</a>

<?php } ?>

</div>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>