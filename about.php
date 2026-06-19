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
   <title data-ar="من نحن" data-en="about us" ></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- about section starts  -->

<section class="about">

   <div class="row">

      <div class="image">
         <img src="images/about-us.png" alt="about us">
      </div>

      <div class="content">
         <h3 data-en="why choose us?" data-ar="من نحن؟"></h3>
         <p data-ar="منصة اتقان التعليمية الإلكترونية توفر موارد تعليمية متنوعة في مكان واحد، تشمل الفيديوهات، الكتب الرقمية، والتفاعل مع مساعد ذكي يعتمد على الذكاء الاصطناعي للإجابة على أسئلة الطلاب. نسعى لتسهيل التعلم وتقديم أدوات فعالة للطلاب والمعلمين. تتيح منصتنا الوصول إلى محتوى تعليمي عالي الجودة في مختلف المجالات والمستويات، مع إمكانية البحث عن مدرسين متميزين لتلبية احتياجات كل طالب. نؤمن بأن التعليم هو مفتاح النجاح، ونعمل على توفير الموارد التي تساعد المتعلمين في تحقيق أهدافهم وتطوير مهاراتهم. انضم إلينا في رحلة التعلم المستمر."
         data-en="The e-learning Itqan platform provides various educational resources in one place, including videos, digital books, and interaction with a smart assistant based on artificial intelligence to answer students’ questions. We seek to facilitate learning and provide effective tools for students and teachers. Our platform provides access to high-quality educational content in various fields and levels, with the ability to search for distinguished teachers to meet the needs of each student. We believe that education is the key to success, and we work to provide resources that help learners achieve their goals and develop their skills. Join us on a journey of continuous learning.">
         </p>
         <div class="container-about">
            <a href="courses.php" class="inline-btn" data-en="our courses" data-ar="الدورات"></a>
            <a href="assistant.php" class="inline-option-btn" data-en="Smart Assistant" data-ar="المساعد الذكي"></a>
            <a href="books.php" class="inline-btn" data-en="books" data-ar="الكتب"></a>
         </div>
      </div>

   </div>


<!-- about section ends -->











<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

   
</body>
</html>