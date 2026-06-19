
<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){

   $id = unique_id();

   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);

   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING);

   $cpass = sha1($_POST['cpass']);
   $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

   // الحساب مفعل
   $is_active = 1;

   // التحقق من الإيميل
   $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
   $select_user->execute([$email]);

   if($select_user->rowCount() > 0){

      $message[] = 'email already taken!';

   }else{

      if($pass != $cpass){

         $message[] = 'confirm password not matched!';

      }else{

         // الصورة
         $image = $_FILES['image']['name'];
         $image = filter_var($image, FILTER_SANITIZE_STRING);

         $ext = pathinfo($image, PATHINFO_EXTENSION);
         $rename = unique_id().'.'.$ext;

         $image_tmp_name = $_FILES['image']['tmp_name'];
         $image_folder = 'uploaded_files/'.$rename;

         // إدخال المستخدم
         $insert_user = $conn->prepare("
            INSERT INTO `users`
            (id, name, email, password, image, is_active)
            VALUES (?,?,?,?,?,?)
         ");

         $insert_user->execute([
            $id,
            $name,
            $email,
            $cpass,
            $rename,
            1
         ]);

         move_uploaded_file($image_tmp_name, $image_folder);

         // تسجيل دخول مباشر
         $verify_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND password = ? LIMIT 1");
         $verify_user->execute([$email, $pass]);

         if($verify_user->rowCount() > 0){

            $row = $verify_user->fetch(PDO::FETCH_ASSOC);

            setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');

            header('location:home.php');
         }

      }

   }

}

?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="تسجيل حساب" data-en="register"></title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>
<body>



<section class="form-container">

   <form class="register" action="" method="post" enctype="multipart/form-data">
      <h3 data-ar="إنشاء حساب" data-en="create account"></h3>
      <div class="flex">
         <div class="col">
            <p data-ar="اسمك" data-en="your name"><span>*</span></p>
            <input type="text" name="name" data-ar="ادخل اسمك" data-en="enter your name" maxlength="50" required class="box">
            <p data-ar="بريدك الالكتروني" data-en="your email"> <span>*</span></p>
            <input type="email" name="email" data-ar="ادخل بريدك الالكتروني" data-en="enter your email" maxlength="20" required class="box">
         </div>
         <div class="col">
            <p data-ar="كلمة السر الخاصة بك" data-en="your password"><span>*</span></p>
            <input type="password" name="pass" data-ar="ادخل كلمة السر" data-en="enter your password" maxlength="20" required class="box">
            <p data-ar="تأكيد كلمة السر" data-en="confirm password"><span>*</span></p>
            <input type="password" name="cpass" data-ar="تأكيد كلمة السر" data-en="confirm your password" maxlength="20" required class="box">
         </div>
      </div>
      <p data-ar="الصورة الشخصية" data-en="select picture"><span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
     <a class="link" href="login.php" data-ar="لديك حساب بالفعل؟" data-en="already have an account?">
   already have an account?
   <span data-ar="تسجيل الدخول الآن" data-en="login now"></span>
</a> <input type="submit" name="submit" data-ar="سجل الآن" data-en="register now" class="btn">
   </form>

</section>












<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>