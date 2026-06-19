<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
   header('location:login.php');
   exit(); // Ensure script stops executing after redirection
}

$select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
$select_likes->execute([$user_id]);
$total_likes = $select_likes->rowCount();

$select_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
$select_comments->execute([$user_id]);
$total_comments = $select_comments->rowCount();

$select_bookmark = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ?");
$select_bookmark->execute([$user_id]);
$total_bookmarked = $select_bookmark->rowCount();


?>

<!DOCTYPE html>
<html lang="ar">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="الملف الشخصي" data-en="profile"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="profile">

   <h1 class="heading" data-ar="تفاصيل الملف الشخصي" data-en="profile details"></h1>

   <div class="details">

      <div class="user">
         <img src="uploaded_files/<?= $fetch_profile['image']; ?>" alt="">
         <h3><?= $fetch_profile['name']; ?></h3>
         <p data-ar="طالب" data-en="student"></p>
         <a href="update.php" class="inline-btn" data-ar="تحديث الملف الشخصي" data-en="update profile"></a>
      </div>

      <div class="box-container">

         <div class="box">
            <div class="flex">
               <i class="fas fa-bookmark"></i>
               <div>
                  <h3><?= $total_bookmarked; ?></h3>
                  <span data-ar="قوائم التشغيل المحفوظة" data-en="saved playlists"></span>
               </div>
            </div>
            <a href="bookmark.php" class="inline-btn" data-ar="عرض قوائم التشغيل" data-en="view playlists"></a>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-heart"></i>
               <div>
                  <h3><?= $total_likes; ?></h3>
                  <span data-ar="مقاطع الفيديو التي اعجبتك" data-en="liked tutorials"></span>
               </div>
            </div>
            <a href="likes.php" class="inline-btn" data-ar="عرض الاعجابات" data-en="view liked"></a>
         </div>

         <div class="box">
            <div class="flex">
               <i class="fas fa-comment"></i>
               <div>
                  <h3><?= $total_comments; ?></h3>
                  <span data-ar="تعليقاتك لمقاطع الفيديو" data-en="video comments"></span>
               </div>
            </div>
            <a href="comments.php" class="inline-btn" data-ar="رؤية التعليقات" data-en="view comments"></a>
         </div>

      </div>

   </div>

</section>

<!-- profile section ends -->

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>
