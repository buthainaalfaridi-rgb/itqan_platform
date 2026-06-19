<?php
include 'components/connect.php';

$user_id = $_COOKIE['user_id'] ?? '';

if($user_id == ''){
   header('location:login.php');
   exit();
}

$message = [];

/* =======================
   DELETE FILE
======================= */
if(isset($_GET['delete'])){
   $delete_id = (int)$_GET['delete'];

   $check = $conn->prepare("SELECT * FROM upload WHERE id = ? AND user_id = ?");
   $check->execute([$delete_id, $user_id]);

   if($check->rowCount() > 0){
      $file = $check->fetch(PDO::FETCH_ASSOC);
      if(file_exists($file['file_path'])){
         unlink($file['file_path']);
      }

      $delete = $conn->prepare("DELETE FROM upload WHERE id = ? AND user_id = ?");
      $delete->execute([$delete_id, $user_id]);

      $message[] = "تم حذف الملف بنجاح";
   }
}

/* =======================
   UPLOAD MULTIPLE FILES WITH HASH
======================= */
$allowed_exts = ['pdf','docx','txt'];

if(isset($_POST['upload_files']) && isset($_FILES['files'])){
    foreach($_FILES['files']['name'] as $index => $name){
        $tmp  = $_FILES['files']['tmp_name'][$index];
        $size = $_FILES['files']['size'][$index];
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowed_exts)){
            $message[] = "الملف {$name} غير مدعوم، فقط PDF, DOCX, TXT مسموح.";
            continue;
        }

        // حساب hash للملف
        $file_hash = md5_file($tmp);

        // تحقق من التكرار في قاعدة البيانات
        $check_existing = $conn->prepare("SELECT * FROM upload WHERE user_id = ? AND (file_name = ? OR file_hash = ?)");
        $check_existing->execute([$user_id, $name, $file_hash]);

        if($check_existing->rowCount() > 0){
            $message[] = "الملف {$name} موجود مسبقاً!";
            continue;
        }

        $upload_dir = "uploaded_files/";
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }

        $new_name = time().'_'.$name;
        $file_path = $upload_dir.$new_name;

        if(move_uploaded_file($tmp, $file_path)){
            $insert = $conn->prepare("INSERT INTO upload(user_id,file_name,file_path,file_size,file_hash) VALUES(?,?,?,?,?)");
            $insert->execute([$user_id,$name,$file_path,$size,$file_hash]);
            $message[] = "تم رفع الملف {$name} بنجاح";
        } else {
            $message[] = "حدث خطأ أثناء رفع الملف {$name}";
        }
    }
}

/* =======================
   FETCH USER FILES
======================= */
$select_files = $conn->prepare("SELECT * FROM upload WHERE user_id = ? ORDER BY id DESC");
$select_files->execute([$user_id]);
$files = $select_files->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="ملفاتي" data-en="My Files"></title>
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="books">
   <h1 class="heading" data-ar="ملفاتي" data-en="My Files"></h1>

   <!-- نموذج رفع الملفات -->
   <form method="post" enctype="multipart/form-data" class="search-book">
       <input type="file" name="files[]" accept=".pdf,.docx,.txt" multiple required data-ar="اختر الملفات ..." data-en="Select files...">
       <button type="submit" name="upload_files" class="fas fa-upload" data-ar="رفع" data-en="Upload"></button>
   </form>



   <div class="box-container-book">
    
      <?php if($files): ?>
         <?php foreach($files as $file): ?>
            <div class="box-container-book">
               <div class="box-book">
                  <div class="box">
                   <i class="fas fa-file" style="font-size:50px; color:#e74c3c;"></i>
                   <h3 class="title-book"><?= htmlspecialchars($file['file_name']); ?></h3>
                   <div class="author-container">
                       <p class="author-book" data-ar="نوع الملف" data-en="File Type">
                           <span><?= pathinfo($file['file_name'], PATHINFO_EXTENSION); ?></span>
                       </p>
                   </div>
                   <div class="box">
                       <a href="<?= $file['file_path']; ?>" target="_blank" class="inline-btn" data-ar="قراءة" data-en="Read"></a>
                       <a href="<?= $file['file_path']; ?>" download class="inline-option-btn" data-ar="تحميل" data-en="Download"></a>
                       <a href="?delete=<?= $file['id']; ?>" onclick="return confirm('هل أنت متأكد؟')" class="inline-delete-btn" data-ar="حذف" data-en="Delete"></a>
                       <a href="summarize.php?file_id=<?= $file['id']; ?>" class="inline-btn">تلخيص</a>
                       </div>
                   </div>
               </div>
            </div>
         <?php endforeach; ?>
      <?php else: ?>
         <p class="empty" data-ar="لا توجد ملفات بعد" data-en="No files yet"></p>
      <?php endif; ?>
   </div>
</section>

<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>