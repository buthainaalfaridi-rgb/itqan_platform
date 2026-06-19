<?php

include 'components/connect.php';

$user_id = $_COOKIE['user_id'] ?? '';

if (!isset($_GET['get_id'])) {
    header('location:home.php');
    exit();
}

$get_id = $_GET['get_id'];

/* ================= FETCH PLAYLIST ================= */

$select_playlist = $conn->prepare(
    "SELECT * FROM `playlist` WHERE id = ? AND status = ? LIMIT 1"
);
$select_playlist->execute([$get_id, 'active']);

if ($select_playlist->rowCount() == 0) {
    echo '<p class="empty" data-ar="لم يتم العثور على قائمة التشغيل هذه!" data-en="This playlist was not found!"></p>';
    exit();
}

$fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC);
$playlist_id   = $fetch_playlist['id'];

/* ================= BOOKMARK ================= */
$select_bookmark = $conn->prepare("
    SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?
");
$select_bookmark->execute([$user_id, $playlist_id]);

$is_bookmarked = $select_bookmark->rowCount() > 0;

/* ================= HANDLE SAVE / REMOVE BOOKMARK ================= */

if (isset($_POST['save_list'])) {

    if ($user_id == '') {

        $message[] = '
        <span data-ar="الرجاء تسجيل الدخول أولاً!" data-en="Please login first!">
        الرجاء تسجيل الدخول أولاً!
        </span>';

    } else {

        $list_id = filter_var($_POST['list_id'], FILTER_SANITIZE_NUMBER_INT);

        // ✅ FIX: ما في id في الجدول
        $check = $conn->prepare("
            SELECT 1 FROM bookmark 
            WHERE user_id = ? AND playlist_id = ? 
            LIMIT 1
        ");
        $check->execute([$user_id, $list_id]);

        if ($check->rowCount() > 0) {

            $remove = $conn->prepare("
                DELETE FROM bookmark 
                WHERE user_id = ? AND playlist_id = ?
            ");
            $remove->execute([$user_id, $list_id]);

            $message[] = '
            <span data-ar="تمت إزالة قائمة التشغيل من المحفوظات!" data-en="Playlist removed from bookmarks!">
            تمت إزالة قائمة التشغيل من المحفوظات!
            </span>';

        } else {

            $insert = $conn->prepare("
                INSERT INTO bookmark (user_id, playlist_id) VALUES (?, ?)
            ");
            $insert->execute([$user_id, $list_id]);

            $message[] = '
            <span data-ar="تم حفظ قائمة التشغيل!" data-en="Playlist saved successfully!">
            تم حفظ قائمة التشغيل!
            </span>';
        }

        header("Location: playlist.php?get_id=" . $list_id);
        exit();
    }
}

/* ================= COUNT QUESTIONS ================= */

$q_count_stmt = $conn->prepare(
    "SELECT COUNT(*) as total FROM playlist_questions WHERE playlist_id = ?"
);
$q_count_stmt->execute([$playlist_id]);
$q_count = $q_count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

/* ================= CHECK CERTIFICATE ================= */

$certificate_check = false;

if($user_id != ''){

   $check_certificate = $conn->prepare("
      SELECT id
      FROM quiz_certificates
      WHERE student_id = ?
      AND playlist_id = ?
      LIMIT 1
   ");

   $check_certificate->execute([$user_id, $playlist_id]);

   $certificate_check = $check_certificate->rowCount() > 0;
}
/* ================= CATEGORY ================= */

$select_category = $conn->prepare(
    "SELECT name, name_en FROM `categories` WHERE id = ? LIMIT 1"
);
$select_category->execute([$fetch_playlist['category_id']]);
$fetch_category = $select_category->fetch(PDO::FETCH_ASSOC);

/* ================= TUTOR ================= */

$select_tutor = $conn->prepare(
    "SELECT * FROM `tutors` WHERE id = ? LIMIT 1"
);
$select_tutor->execute([$fetch_playlist['tutor_id']]);
$fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

/* ================= VIDEOS ================= */

$select_total_videos = $conn->prepare(
    "SELECT COUNT(*) FROM `content` WHERE playlist_id = ? AND status = ?"
);
$select_total_videos->execute([$playlist_id, 'active']);
$total_videos = $select_total_videos->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title data-ar="قائمة التشغيل" data-en="Playlist">قائمة التشغيل</title>

<link rel="stylesheet" href="css/all.min.css">
<link id="style" rel="stylesheet" href="css/style-ar.css">
<Style>
    .quiz-finished{
    margin-top:10px;
    padding:12px;
    background: transparent;
    color: #45abbd;
    border-radius:8px;
    font-weight:bold;
    text-align:center;
}
</Style>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="playlist">

<h1 class="heading" data-ar="تفاصيل قائمة التشغيل" data-en="Playlist Details"></h1>

<div class="row">

<div class="col">

<div class="thumb">
<span><?= $total_videos; ?> Videos</span>
<img src="uploaded_files/<?= htmlspecialchars($fetch_playlist['thumb']); ?>">
</div>

</div>

<div class="col">

<div class="tutor">
<img src="uploaded_files/<?= htmlspecialchars($fetch_tutor['image']); ?>">
<div>
<h3><?= htmlspecialchars($fetch_tutor['name']); ?></h3>
<span><?= htmlspecialchars($fetch_tutor['profession_ar']); ?></span>
</div>
</div>

<div class="details">

<a href="courses.php?category_id=<?= $fetch_playlist['category_id']; ?>">
<span
data-ar="<?= htmlspecialchars($fetch_category['name']); ?>"
data-en="<?= htmlspecialchars($fetch_category['name_en']); ?>">
<?= htmlspecialchars($fetch_category['name']); ?>
</span>
</a>
&emsp;
&emsp;
<a href="chat.php?receiver_type=teacher&receiver_id=<?= $fetch_playlist['tutor_id']; ?>" class="inline-btn">
   <i class="fas fa-comments"></i>
   مراسلة المعلم
</a>
<h3><?= htmlspecialchars($fetch_playlist['title']); ?></h3>
<p><?= htmlspecialchars($fetch_playlist['description']); ?></p>

<div class="date">
<i class="fas fa-calendar"></i>
<span><?= htmlspecialchars($fetch_playlist['date']); ?></span>
</div>

<!-- ⭐ زر الحفظ -->
<form method="post" class="save-list">

    <input type="hidden" name="list_id" value="<?= $playlist_id; ?>">

   <?php if ($is_bookmarked) { ?>

        <button type="submit" name="save_list" class="inline-btn">
            <i class="fas fa-bookmark"></i>
            <span data-ar="محفوظة" data-en="Saved">محفوظة</span>
        </button>

    <?php } else { ?>

        <button type="submit" name="save_list" class="inline-btn">
            <i class="far fa-bookmark"></i>
            <span data-ar="حفظ القائمة" data-en="Save Playlist">حفظ القائمة</span>
        </button>

    <?php } ?>

</form>

<!-- 🔥 الاختبار -->
<!-- 🔥 الاختبار -->
<div class="quiz-inline">

<?php if($q_count > 0): ?>

   <?php if($certificate_check): ?>

      <p class="quiz-finished">
         ✅ لقد أتممت الاختبار مسبقاً
      </p>
     <a href="certificate.php?playlist_id=<?= $playlist_id; ?>"
      class="inline-btn">
         انظر الى شهادتك
      </a>

   <?php else: ?>

      <p class="quiz-text">
         هل أنت جاهز للاختبار؟
      </p>

      <a href="quiz_start.php?playlist_id=<?= $playlist_id; ?>"
      class="inline-btn">
         ابدأ الاختبار
      </a>

   <?php endif; ?>

<?php else: ?>

   <p class="no-quiz">
      🚫 لا يوجد اختبار لهذه القائمة
   </p>

<?php endif; ?>

</div>
</div>
</div>
</div>

</section>

<section class="videos-container">

<h1 class="heading" data-ar="فيديوهات قائمة التشغيل" data-en="Playlist Videos"></h1>

<div class="box-container">

<?php
$select_content = $conn->prepare(
"SELECT * FROM `content` WHERE playlist_id = ? AND status = ? ORDER BY date DESC"
);
$select_content->execute([$playlist_id, 'active']);

if ($select_content->rowCount() > 0) {
while ($video = $select_content->fetch(PDO::FETCH_ASSOC)) {
?>

<a href="watch_video.php?get_id=<?= $video['id']; ?>" class="box">
<i class="fas fa-play"></i>
<img src="uploaded_files/<?= htmlspecialchars($video['thumb']); ?>">
<h3><?= htmlspecialchars($video['title']); ?></h3>
</a>

<?php
}
} else {
echo '<p class="empty" data-ar="لا توجد فيديوهات بعد!" data-en="No videos added yet!"></p>';
}
?>

</div>
</section>

<style>
.quiz-inline { margin-top: 15px; }
.quiz-text { margin-bottom: 8px; font-weight: 500; color: #555; }
.no-quiz { color: #999; font-size: 14px; }

.save-list {
    margin-top: 10px;
}
</style>

<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>