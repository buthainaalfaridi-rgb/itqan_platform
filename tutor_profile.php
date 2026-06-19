<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['tutor_fetch'])){

   $tutor_email = $_POST['tutor_email'];
   $tutor_email = filter_var($tutor_email, FILTER_SANITIZE_STRING);
   $select_tutor = $conn->prepare('SELECT * FROM `tutors` WHERE email = ?');
   $select_tutor->execute([$tutor_email]);

   $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
   $tutor_id = $fetch_tutor['id'];

   $count_playlists = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $count_playlists->execute([$tutor_id]);
   $total_playlists = $count_playlists->rowCount();

   $count_contents = $conn->prepare("SELECT * FROM `content` WHERE tutor_id = ?");
   $count_contents->execute([$tutor_id]);
   $total_contents = $count_contents->rowCount();

   $count_likes = $conn->prepare("SELECT * FROM `likes` WHERE tutor_id = ?");
   $count_likes->execute([$tutor_id]);
   $total_likes = $count_likes->rowCount();

   $count_comments = $conn->prepare("SELECT * FROM `comments` WHERE tutor_id = ?");
   $count_comments->execute([$tutor_id]);
   $total_comments = $count_comments->rowCount();

}else{
   header('location:teachers.php');
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="الملف الشخصي للمعلم" data-en="tutor's profile"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- teachers profile section starts  -->

<section class="tutor-profile">

   <h1 class="heading" data-ar="تفاصيل الملف الشخصي" data-en="profile details"></h1>

   <div class="details">
      <div class="tutor">
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
         <span data-ar="<?= htmlspecialchars($fetch_tutor['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_tutor['profession']); ?>">
            <?= htmlspecialchars($fetch_tutor['profession_ar']); // default to Arabic ?> 
         </span>   
      </div>
      <div class="flex">
        <p>
   <span data-ar="إجمالي قوائم التشغيل" data-en="total playlists">
      إجمالي قوائم التشغيل
   </span>
   : <span><?= $total_playlists; ?></span>
</p>

<p>
   <span data-ar="إجمالي الفيديوهات" data-en="total videos">
      إجمالي الفيديوهات
   </span>
   : <span><?= $total_contents; ?></span>
</p>

<p>
   <span data-ar="إجمالي الاعجابات" data-en="total likes">
      إجمالي الاعجابات
   </span>
   : <span><?= $total_likes; ?></span>
</p>

<p>
   <span data-ar="إجمالي التعليقات" data-en="total comments">
      إجمالي التعليقات
   </span>
   : <span><?= $total_comments; ?></span>
</p>
      </div>
   </div>

</section>

<!-- teachers profile section ends -->

<section class="courses">

   <h1 class="heading" data-ar="أحدث الدورات" data-en="latest courese"></h1>

   <div class="box-container">

      <?php
         $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ? AND status = ?");
         $select_courses->execute([$tutor_id, 'active']);
         if($select_courses->rowCount() > 0){
            while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
               $course_id = $fetch_course['id'];

               $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
               $select_tutor->execute([$fetch_course['tutor_id']]);
               $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_tutor['name']; ?></h3>
               <span><?= $fetch_course['date']; ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= $fetch_course['thumb']; ?>" class="thumb" alt="">
         <h3 class="title"><?= $fetch_course['title']; ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn" data-ar="عرض قائمة التشغيل" data-en="view playlist"></a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty" data-ar="لا يوجد دورات مضافة!" data-en="no courses added yet!"></p>';
      }
      ?>

   </div>

</section>

<!-- courses section ends -->










<?php include 'components/footer.php'; ?>    

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>