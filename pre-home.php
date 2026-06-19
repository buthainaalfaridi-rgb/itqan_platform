<?php
include 'components/connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-en="Home" data-ar="الرئيسية">الرئيسية</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lateef:wght@200;300;400;500;600;700;800&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Lateef:wght@200;300;400;500;600;700;800&family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">
    <!-- custom css file link  -->
    <link rel="preload" href="css/pre-home.css" as="style">
    <link id="style" rel="stylesheet" href="css/pre-home-en.css">

</head>

<body>
<section class='wrapper'>
    <div class='hero'></div>
    <div class='content'>
        <div class='image'>
            <img src='images/platform.png' alt='Descriptive Alt Text' />
        </div>
        <div class='title-and-text'>
            <h1 class='h1--scalingSize' data-en='Al-Maarifa Platform' data-ar="مـنـصـة الـمـعـرفـة"></h1>
            <p class='subtitle' data-ar="مرحباً بكم في منصة التعلم الإلكتروني، حيث تبدأ المعرفة..." data-en="Welcome to E-learning platform, where Knowledge begins ... "></p>
        </div>
        <div class='buttons'>
            <a class='btn btn-register' href="register.php" data-ar="سجل الآن" data-en="Register Now"></a>
            <a class='btn btn-signin' href="login.php" data-ar="تسجيل دخول" data-en="Login"></a>
        </div>    
        <div class="icon-container">
            <div class="dark-mode-switch"> <input type='checkbox' id='switch'>
                <label for='switch' class='switch-label'>
                <span id='dark-icon' class="material-symbols-outlined">dark_mode</span>
                <span id='light-icon' class="material-symbols-outlined">light_mode</span>
                </label>
            </div>
            <span id='lang-switcher' onclick="switchLanguage()" class="material-symbols-outlined">language</span>
        </div>
    </div>            
</section>
<script src="js/switcher-pre-home.js" defer></script>
</body>
</html>
