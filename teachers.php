<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="المعلمين" data-en="tutors">المعلمين</title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="preload" href="css/style-en.css" as="style">
   <link rel="preload" href="css/style-ar.css" as="style">
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- teachers section starts  -->

<section class="teachers">

   <h1 class="heading" data-ar="المعلمين" data-en="Tutors"></h1>

   <form action="search_tutor.php" method="post" class="search-tutor">
      <input type="text" name="search_tutor" maxlength="100" data-ar="ابحث عن معلم ..." data-en="search tutor..." required>
      <button type="submit" name="search_tutor_btn" class="fas fa-search"></button>
   </form>

   <div class="box-container">

      <!-- <div class="box offer">
         <h3>become a tutor</h3>
         <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Laborum, magnam!</p>
         <a href="admin/register.php" class="inline-btn">get started</a>
      </div> -->

      <?php
         $select_tutors = $conn->prepare("SELECT * FROM `tutors`");
         $select_tutors->execute();
         if($select_tutors->rowCount() > 0){
            while($fetch_tutor = $select_tutors->fetch(PDO::FETCH_ASSOC)){

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
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
               <span data-ar="<?= htmlspecialchars($fetch_tutor['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_tutor['profession']); ?>">
                  <?= htmlspecialchars($fetch_tutor['profession_ar']); // default to Arabic ?> 
               </span>              
            </div>
         </div>
       <p>
   <span class="label" data-ar="إجمالي قوائم التشغيل" data-en="total playlists">
      إجمالي قوائم التشغيل
   </span>
   :
   <span class="count"><?= $total_playlists; ?></span>
</p>

<p>
   <span class="label" data-ar="إجمالي الفيديوهات" data-en="total videos">
      إجمالي الفيديوهات
   </span>
   :
   <span class="count"><?= $total_contents; ?></span>
</p>

<p>
   <span class="label" data-ar="إجمالي الاعجابات" data-en="total likes">
      إجمالي الاعجابات
   </span>
   :
   <span class="count"><?= $total_likes; ?></span>
</p>

<p>
   <span class="label" data-ar="إجمالي التعليقات" data-en="total comments">
      إجمالي التعليقات
   </span>
   :
   <span class="count"><?= $total_comments; ?></span>
</p>
         <form action="tutor_profile.php" method="post">
            <input type="hidden" name="tutor_email" value="<?= $fetch_tutor['email']; ?>">
            <input type="submit" value="view_profile" data-ar="عرض الملف الشخصي" data-en="view profile" name="tutor_fetch" class="inline-btn">
            <a href="chat.php?receiver_type=teacher&receiver_id=<?= $fetch_tutor['id']; ?>" class="inline-btn">
   <i class="fas fa-comments"></i>
   مراسلة المعلم
</a>
         </form>
      </div>
      <?php
            }
         }else{
            echo '<p class="empty" data-ar="لم يتم العثور على معلمين" data-en="no tutors found!"></p>';
         }
      ?>

   </div>

</section>

<!-- teachers section ends -->






























<?php include 'components/footer.php'; ?>    

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>