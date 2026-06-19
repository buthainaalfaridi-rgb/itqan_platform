<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_GET['get_id'])){
   $get_id = $_GET['get_id'];
}else{
   $get_id = '';
   header('location:home.php');
}

if(isset($_POST['like_content'])){

   if($user_id != ''){

      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
      $select_likes->execute([$user_id, $content_id]);

      if($select_likes->rowCount() > 0){
         $remove_likes = $conn->prepare("DELETE FROM `likes` WHERE user_id = ? AND content_id = ?");
         $remove_likes->execute([$user_id, $content_id]);
         $message[] = '<span data-ar="تمت الإزالة من الإعجابات!" data-en="removed from likes!"></span>';
      }else{
         $insert_likes = $conn->prepare("INSERT INTO `likes`(user_id, tutor_id, content_id) VALUES(?,?,?)");
         $insert_likes->execute([$user_id, $tutor_id, $content_id]);
         $message[] = '<span data-ar="تمت الإضافة إلى الإعجابات!" data-en="added to likes!"></span>';
      }

   }else{
      $message[] = '<span data-ar="الرجاء تسجيل الدخول أولا!" data-en="please login first!"></span>';
   }

}

if(isset($_POST['add_comment'])){

   if($user_id != ''){

      $id = unique_id();
      $comment_box = $_POST['comment_box'];
      $comment_box = filter_var($comment_box, FILTER_SANITIZE_STRING);
      $content_id = $_POST['content_id'];
      $content_id = filter_var($content_id, FILTER_SANITIZE_STRING);

      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? LIMIT 1");
      $select_content->execute([$content_id]);
      $fetch_content = $select_content->fetch(PDO::FETCH_ASSOC);

      $tutor_id = $fetch_content['tutor_id'];

      if($select_content->rowCount() > 0){

         $select_comment = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ? AND user_id = ? AND tutor_id = ? AND comment = ?");
         $select_comment->execute([$content_id, $user_id, $tutor_id, $comment_box]);

         if($select_comment->rowCount() > 0){
            $message[] = '<span data-ar="تمت إضافة التعليق مسبقا!" data-en="comment already added!"></span>';
         }else{
            $insert_comment = $conn->prepare("INSERT INTO `comments`(id, content_id, user_id, tutor_id, comment) VALUES(?,?,?,?,?)");
            $insert_comment->execute([$id, $content_id, $user_id, $tutor_id, $comment_box]);
            $message[] = '<span data-ar="تمت إضافة تعليق جديد!" data-en="new comment added!"></span>';
         }

      }else{
         $message[] = '<span data-ar="حدث خطأ ما!" data-en="something went wrong!"></span>';
      }

   }else{
      $message[] = '<span data-ar="الرجاء تسجيل الدخول أولا!" data-en="please login first!"></span>';
   }

}

if(isset($_POST['delete_comment'])){

   $delete_id = $_POST['comment_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ?");
   $verify_comment->execute([$delete_id]);

   if($verify_comment->rowCount() > 0){
      $delete_comment = $conn->prepare("DELETE FROM `comments` WHERE id = ?");
      $delete_comment->execute([$delete_id]);
      $message[] = '<span data-ar="تم حذف التعليق بنجاح!" data-en="comment deleted successfully!"></span>';
   }else{
      $message[] = '<span data-ar="تم حذف التعليق مسبقا!" data-en="comment already deleted!"></span>';
   }

}

if(isset($_POST['update_now'])){

   $update_id = $_POST['update_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $update_box = $_POST['update_box'];
   $update_box = filter_var($update_box, FILTER_SANITIZE_STRING);

   $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? AND comment = ?");
   $verify_comment->execute([$update_id, $update_box]);

   if($verify_comment->rowCount() > 0){
      $message[] = '<span data-ar="تمت إضافة التعليق مسبقا!" data-en="comment already added!"></span>';
   }else{
      $update_comment = $conn->prepare("UPDATE `comments` SET comment = ? WHERE id = ?");
      $update_comment->execute([$update_box, $update_id]);
      $message[] = '<span data-ar="تم تعديل التعليق بنجاح!" data-en="comment edited successfully!"></span>';
   }

}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="مشاهدة فيديو" data-en="watch video"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
   if(isset($_POST['edit_comment'])){
      $edit_id = $_POST['comment_id'];
      $edit_id = filter_var($edit_id, FILTER_SANITIZE_STRING);
      $verify_comment = $conn->prepare("SELECT * FROM `comments` WHERE id = ? LIMIT 1");
      $verify_comment->execute([$edit_id]);
      if($verify_comment->rowCount() > 0){
         $fetch_edit_comment = $verify_comment->fetch(PDO::FETCH_ASSOC);
?>
<section class="edit-comment">
   <h1 class="heading" data-ar="تعديل التعليق" data-en="edti comment">تعديل التعليق</h1>
   <form action="" method="post">
         <input type="hidden" name="update_id" value="<?= $fetch_edit_comment['id']; ?>">
         <textarea id="updateBox" name="update_box" class="box" maxlength="1000" required data-ar="من فضلك، ادخل تعليقك" data-en="please enter your comment" cols="30" rows="10"><?= $fetch_edit_comment['comment']; ?></textarea>
         <div class="flex">
            <a href="watch_video.php?get_id=<?= $get_id; ?>" class="inline-option-btn" name="cancel_edit" data-ar="الغاء التعديل" data-en="cancel edit"></a>
            <input type="submit" value="update now" name="update_now" data-ar="تحديث الآن" data-en="update now" class="inline-btn" id="updateButton">
         </div>
   </form>
</section>
<?php
   }else{
      $message[] = '<span data-ar="لم يتم العثور على التعليق!" data-en="comment was not found!"></span>';
   }
}
?>

<!-- watch video section starts  -->

<section class="watch-video">

   <?php
      $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
      $select_content->execute([$get_id, 'active']);
      if($select_content->rowCount() > 0){
         while($fetch_content = $select_content->fetch(PDO::FETCH_ASSOC)){
            $content_id = $fetch_content['id'];

            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ?");
            $select_likes->execute([$content_id]);
            $total_likes = $select_likes->rowCount();  

            $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND content_id = ?");
            $verify_likes->execute([$user_id, $content_id]);

            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
            $select_tutor->execute([$fetch_content['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);


               // Retrieve questions and options based on content_id
               $select_mcq = $conn->prepare("SELECT * FROM `mcq` WHERE content_id = ?");
               $select_mcq->execute([$content_id]);
               $questions = $select_mcq->fetchAll(PDO::FETCH_ASSOC);

   ?>
   <div class="video-details">
      <video src="uploaded_files/<?= $fetch_content['video']; ?>" class="video" poster="uploaded_files/<?= $fetch_content['thumb']; ?>" controls autoplay></video>
      <h3 class="title"><?= $fetch_content['title']; ?></h3>
      <div class="info">
         <p><i class="fas fa-calendar"></i><span><?= $fetch_content['date']; ?></span></p>
         <p><i class="fas fa-heart" data-ar=" اعجابات" data-en=" likes"></i><span><?= $total_likes; ?> </span></p>
      </div>
      <div class="tutor">
         <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="">
         <div>
         <h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
         <span data-ar="<?= htmlspecialchars($fetch_tutor['profession_ar']); ?>" data-en="<?= htmlspecialchars($fetch_tutor['profession']); ?>">
            <?= htmlspecialchars($fetch_tutor['profession_ar']); // default to Arabic ?> 
         </span>   
         </div>
      </div>
      <form action="" method="post" class="flex">
         <input type="hidden" name="content_id" value="<?= $content_id; ?>">
         <a href="playlist.php?get_id=<?= $fetch_content['playlist_id']; ?>" class="inline-btn" data-ar="عرض قائمة التشغيل" data-en="view playlist"></a>
         <input type="hidden" name="content_id" value="<?= $content_id; ?>">

         <?php
            if($verify_likes->rowCount() > 0){
         ?>
         <button type="submit" name="like_content"><i class="fas fa-heart"></i><span data-ar="أعجبت" data-en="liked"></span></button>
         <?php
         }else{
         ?>
         <button type="submit" name="like_content"><i class="far fa-heart"></i><span data-ar="اعجاب" data-en="like"></span></button>
         <?php
            }
         ?>
      </form>
      <button type="button" data-ar="اختبار" data-en="Quiz" class="inline-option-btn" target="_blank" id="takeQuizButton" value="<?= $content_id; ?>" data-content-id="<?= $content_id; ?>">Take Quiz</button>
      <div class="description"><p><?= $fetch_content['description']; ?></p></div>

   </div>
   <?php
         }
      }else{
         echo '<p class="empty" data-ar="لا توجد فيديوهات مضافة حتى الآن! data-en="no videos added yet!"></p>';
      }
   ?>

</section>

<!-- watch video section ends -->

<!-- comments section starts  -->

<section class="comments">

   <h1 class="heading" data-ar="إضافة تعليق" data-en="add a comment"></h1>

   <form action="" method="post" class="add-comment">
    <input type="hidden" name="content_id" value="<?= $get_id; ?>">
    <textarea id="commentBox" name="comment_box" required data-ar="اكتب تعليقك..." data-en="write your comment..." maxlength="1000" cols="30" rows="10"></textarea>
    <input type="submit" value="add comment" data-ar="إضافة تعليق" data-en="add comment" name="add_comment" class="inline-btn" id="submitButton">
</form>

   <h1 class="heading" data-ar="تعليقات المستخدم" data-en="user comments"></h1>

   
   <div class="show-comments">
      <?php
         $select_comments = $conn->prepare("SELECT * FROM `comments` WHERE content_id = ?");
         $select_comments->execute([$get_id]);
         if($select_comments->rowCount() > 0){
            while($fetch_comment = $select_comments->fetch(PDO::FETCH_ASSOC)){   
               $select_commentor = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
               $select_commentor->execute([$fetch_comment['user_id']]);
               $fetch_commentor = $select_commentor->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box" style="<?php if($fetch_comment['user_id'] == $user_id){echo 'order:-1;';} ?>">
         <div class="user">
            <img src="uploaded_files/<?= $fetch_commentor['image']; ?>" alt="">
            <div>
               <h3><?= $fetch_commentor['name']; ?></h3>
               <span><?= $fetch_comment['date']; ?></span>
            </div>
         </div>
         <p class="text"><?= $fetch_comment['comment']; ?></p>
         <?php
            if($fetch_comment['user_id'] == $user_id){ 
         ?>
         <form action="" method="post" class="flex-btn">
            <input type="hidden" name="comment_id" value="<?= $fetch_comment['id']; ?>">
            <button type="submit" data-ar="تعديل التعليق" data-en="edit comment" name="edit_comment" class="inline-option-btn"></button>
            <button type="submit"data-ar="حذف التعليق" data-en="delete comment" name="delete_comment" class="inline-delete-btn" onclick="return confirmAction (event,'delete_comment');"></button>
         </form>
         <?php
         }
         ?>
      </div>
      <?php
      }
      }else{
         echo '<p class="empty" data-ar="ليس هناك تعليقات مضافة حتى الآن!" data-en="no comments added yet!"></p>';
      }
      ?>
      </div>
   
</section>

<!-- comments section ends -->








<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->

<script>
document.getElementById("takeQuizButton").addEventListener("click", function() {
    var contentId = this.getAttribute("data-content-id");
    window.open("quiz.php?content_id=" + contentId, "_blank"); // Open in a new tab
});
</script>
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>