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
   <title data-ar="الدورات" data-en="courses"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- courses section starts  -->

<section class="courses">

   <h1 class="heading" data-ar="نتائج البحث" data-en="search results"></h1>

   <div class="box-container">

      <?php
         if(isset($_POST['search_course']) or isset($_POST['search_course_btn'])){
            // Get the search term from the form
            $search_course = $_POST['search_course'];
            
            // Use a prepared statement with a placeholder for the search term
            $select_courses = $conn->prepare("SELECT * FROM `playlist` WHERE title LIKE ? AND status = ?");
            
            // Execute the prepared statement with the search term wrapped in wildcards for partial matching
            $select_courses->execute(['%' . $search_course . '%', 'active']);
            
            // Check if any courses match the search criteria
            if($select_courses->rowCount() > 0){
               while($fetch_course = $select_courses->fetch(PDO::FETCH_ASSOC)){
                  $course_id = $fetch_course['id'];

                  // Fetch tutor information based on the tutor_id
                  $select_tutor = $conn->prepare("SELECT * FROM `tutor` WHERE id = ?");
                  $select_tutor->execute([$fetch_course['tutor_id']]);
                  $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= htmlspecialchars($fetch_tutor['image']); ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
               <span><?= htmlspecialchars($fetch_course['date']); ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= htmlspecialchars($fetch_course['thumb']); ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($fetch_course['title']); ?></h3>
         <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn" data-ar="عرض قائمة التشغيل" data-en="view playlist"></a>
      </div>
      <?php
               }
            }else{
               echo '<p class="empty" data-ar="لم يتم العثور على دورات!" data-en="no courses found!"></p>';
            }
         }else{
            echo '<p class="empty">please search something!</p>';
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
