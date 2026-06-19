<?php
include 'components/connect.php';

/* =======================
   USER VALIDATION
======================= */
$user_id = $_COOKIE['user_id'] ?? '';

/* =======================
   FETCH USER STATS
======================= */
$total_likes = $conn->prepare("SELECT COUNT(*) FROM `likes` WHERE user_id = ?");
$total_likes->execute([$user_id]);
$total_likes = $total_likes->fetchColumn();

$total_comments = $conn->prepare("SELECT COUNT(*) FROM `comments` WHERE user_id = ?");
$total_comments->execute([$user_id]);
$total_comments = $total_comments->fetchColumn();

$total_bookmarked = $conn->prepare("SELECT COUNT(*) FROM `bookmark` WHERE user_id = ?");
$total_bookmarked->execute([$user_id]);
$total_bookmarked = $total_bookmarked->fetchColumn();

/* =======================
   CATEGORY SELECTION
======================= */
$category_id = $_GET['category_id'] ?? null;
$lang = $_GET['lang'] ?? 'ar';
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo ($lang == 'ar') ? 'rtl' : 'ltr'; ?>">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title data-en="Home" data-ar="الرئيسية">الرئيسية</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="css/all.min.css">
   <link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>
<!-- quick select section starts -->
<section class="quick-select">
   <h1 class="heading" data-en="Quick Options" data-ar="خيارات سريعة">خيارات سريعة</h1>

   <div class="box-container">

      <?php if($user_id != ''): ?>
      <div class="box">
         <h3 class="title" data-en="Likes and Comments" data-ar="الإعجابات والتعليقات"></h3>
         <p data-ar="مجموع الإعجابات" data-en="Total likes">: <span><?= $total_likes; ?></span></p>
         <a href="likes.php" class="inline-btn" data-en="View Likes" data-ar="عرض الإعجابات"></a>
         <p data-ar="مجموع التعليقات" data-en="Total comments">: <span><?= $total_comments; ?></span></p>
         <a href="comments.php" class="inline-btn" data-en="View Comments" data-ar="عرض التعليقات"></a>
         <p data-ar="قوائم التشغيل المحفوظة" data-en="Saved playlists">: <span><?= $total_bookmarked; ?></span></p>
         <a href="bookmark.php" class="inline-btn" data-en="View Bookmarks" data-ar="عرض الإشارات المرجعية"></a>
      </div>
      <?php else: ?>
      <div class="box" style="text-align: center;">
         <h3 class="title" data-ar="من فضلك، قم بتسجيل الدخول أو التسجيل" data-en="Please login or register"></h3>
         <div class="flex-btn" style="padding-top: .5rem;">
            <a href="login.php" class="option-btn" data-ar="دخول" data-en="Login"></a>
            <a href="register.php" class="option-btn" data-ar="تسجيل" data-en="Register"></a>
         </div>
      </div>
      <?php endif; ?>

      <!-- top categories -->
      <div class="box">
         <h3 class="title" data-en="Top Categories" data-ar="أهم الفئات">أهم الفئات</h3>
         <div class="flex">

            <?php
            // Fetch categories from database
            $categories_stmt = $conn->prepare("SELECT * FROM `categories` ORDER BY id ASC");
            $categories_stmt->execute();
            $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

            // Show "All" category first
            echo '<a href="home.php" class="category-link" data-category="all">
                     <span data-en="All" data-ar="الكل">الكل</span>
                  </a>';

            // Loop through categories
            foreach($categories as $cat){
               echo '<a href="home.php?category_id='.$cat['id'].'" class="category-link" data-category="'.$cat['id'].'">
                        <span data-en="'.htmlspecialchars($cat['name_en']).'" data-ar="'.htmlspecialchars($cat['name']).'">'.htmlspecialchars($cat['name']).'</span>
                     </a>';
            }
            ?>

         </div>
      </div>

      <div class="box tutor">
         <h3 class="title" data-en="Become a Tutor" data-ar="أصبح مدرس"></h3>
         <p data-en="If you have skills in providing high-quality courses, join us as a teacher" data-ar="إذا كان لديك مهارات في تقديم دورات ذو جودة عالية، انضم معنا كمدرس"></p>
         <a href="admin/register.php" class="inline-btn" data-en="Get Started" data-ar="البدء"></a>
      </div>

   </div>
</section>
<!-- quick select section ends -->

<!-- courses section starts -->
<section class="courses">
   <h1 class="heading">
      <?php
      if($category_id){
         // Fetch category name from DB
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
      // Fetch courses based on category or all
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

   <div class="more-btn">
      <a href="courses.php" class="inline-option-btn" data-en="View More" data-ar="عرض المزيد"></a>
   </div>

</section>
<!-- courses section ends -->

<?php include 'components/footer.php'; ?>

<script src="js/script.js" defer></script>
<script src="js/switcher.js" defer></script>
</body>
</html>
