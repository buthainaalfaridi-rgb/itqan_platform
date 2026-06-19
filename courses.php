<?php
include 'components/connect.php';

/* =======================
   USER VALIDATION
======================= */
$user_id = $_COOKIE['user_id'] ?? '';

/* =======================
   CATEGORY SELECTION
======================= */
$category_id = $_GET['category_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-ar="الدورات" data-en="Courses">الدورات</title>

   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Top categories section starts -->
<section class="quick-select">
   <h1 class="heading" data-en="Top Categories" data-ar="أهم الفئات">أهم الفئات</h1>
   <div class="box-container">
      <div class="box">
         <div class="flex">
            <?php
            // عرض فئة "الكل" أولاً
            echo '<a href="courses.php" class="category-link" data-category="all"><span data-en="All" data-ar="الكل">الكل</span></a>';

            // جلب الفئات من قاعدة البيانات
            $categories_stmt = $conn->prepare("SELECT * FROM `categories` ORDER BY id ASC");
            $categories_stmt->execute();
            $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach($categories as $cat){
               echo '<a href="courses.php?category_id='.$cat['id'].'" class="category-link" data-category="'.$cat['id'].'">
                        <span data-en="'.htmlspecialchars($cat['name_en']).'" data-ar="'.htmlspecialchars($cat['name']).'">'.htmlspecialchars($cat['name']).'</span>
                     </a>';
            }
            ?>
         </div>
      </div>
   </div>
</section>
<!-- Top categories section ends -->

<!-- courses section starts -->
<section class="courses">
   <h1 class="heading">
      <?php
      if($category_id){
         // جلب اسم الفئة من قاعدة البيانات
         $stmt_cat = $conn->prepare("SELECT * FROM `categories` WHERE id = ? LIMIT 1");
         $stmt_cat->execute([$category_id]);
         if($stmt_cat->rowCount() > 0){
            $cat = $stmt_cat->fetch(PDO::FETCH_ASSOC);
            echo '<span data-en="Courses in '.htmlspecialchars($cat['name_en']).'" data-ar="دورات في '.htmlspecialchars($cat['name']).'">دورات في '.htmlspecialchars($cat['name']).'</span>';
         } else {
            echo '<span data-en="Latest Courses" data-ar="أحدث الدورات">أحدث الدورات</span>';
         }
      } else {
         echo '<span data-en="Latest Courses" data-ar="أحدث الدورات">أحدث الدورات</span>';
      }
      ?>
   </h1>

   <div class="box-container">
      <?php
      // جلب الدورات حسب الفئة أو جميع الدورات
      if($category_id){
         $stmt = $conn->prepare("SELECT * FROM `playlist` WHERE category_id = ? AND status = ? ORDER BY date DESC LIMIT 6");
         $stmt->execute([$category_id, 'active']);
      } else {
         $stmt = $conn->prepare("SELECT * FROM `playlist` WHERE status = ? ORDER BY date DESC LIMIT 6");
         $stmt->execute(['active']);
      }

      if($stmt->rowCount() > 0){
         while($course = $stmt->fetch(PDO::FETCH_ASSOC)){
            $tutor_stmt = $conn->prepare("SELECT * FROM `tutors` WHERE id = ?");
            $tutor_stmt->execute([$course['tutor_id']]);
            $tutor = $tutor_stmt->fetch(PDO::FETCH_ASSOC);
      ?>
      <div class="box">
         <div class="tutor">
            <img src="uploaded_files/<?= htmlspecialchars($tutor['image']); ?>" alt="">
            <div>
               <h3><?= htmlspecialchars($tutor['name']); ?></h3>
               <span><?= htmlspecialchars($course['date']); ?></span>
            </div>
         </div>
         <img src="uploaded_files/<?= htmlspecialchars($course['thumb']); ?>" class="thumb" alt="">
         <h3 class="title"><?= htmlspecialchars($course['title']); ?></h3>
         <a href="playlist.php?get_id=<?= $course['id']; ?>" class="inline-btn" data-en="View Playlist" data-ar="عرض قائمة التشغيل">عرض قائمة التشغيل</a>
      </div>
      <?php
         }
      } else {
         echo '<p class="empty" data-ar="لم تتم إضافة أي دورات بعد!" data-en="No courses added yet!"></p>';
      }
      ?>
   </div>

</section>
<!-- courses section ends -->

<?php include 'components/footer.php'; ?>

<script src="js/script.js" defer></script>
<script src="js/switcher.js" defer></script>
</body>
</html>
