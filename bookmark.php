<?php
include 'components/connect.php';

$user_id = $_COOKIE['user_id'] ?? '';

if ($user_id == '') {
    header('location:home.php');
    exit();
}

/* =======================
   SAVE / REMOVE BOOKMARK
======================= */
if (isset($_POST['save_list'])) {

    $list_id = filter_var($_POST['list_id'], FILTER_SANITIZE_NUMBER_INT);

    $check = $conn->prepare("SELECT * FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
    $check->execute([$user_id, $list_id]);

    if ($check->rowCount() > 0) {
        $remove = $conn->prepare("DELETE FROM `bookmark` WHERE user_id = ? AND playlist_id = ?");
        $remove->execute([$user_id, $list_id]);
    } else {
        $insert = $conn->prepare("INSERT INTO `bookmark` (user_id, playlist_id) VALUES (?, ?)");
        $insert->execute([$user_id, $list_id]);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title data-ar="الاشارات المرجعية" data-en="Bookmarks"></title>

<link rel="stylesheet" href="css/all.min.css">
<link id="style" rel="stylesheet" href="css/style-ar.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="courses">
<h1 class="heading" data-ar="قوائم التشغيل المرجعية" data-en="Bookmarked Playlists"></h1>

<div class="box-container">

<?php
// استعلام لجلب جميع القوائم المحفوظة للمستخدم الحالي
$select_bookmarks = $conn->prepare("
    SELECT p.*, t.name AS tutor_name, t.image AS tutor_image
    FROM `bookmark` b
    INNER JOIN `playlist` p ON b.playlist_id = p.id
    INNER JOIN `tutors` t ON p.tutor_id = t.id
    WHERE b.user_id = ?
    AND p.status = ?
    ORDER BY p.date DESC
");
$select_bookmarks->execute([$user_id, 'active']);

if ($select_bookmarks->rowCount() > 0) {
    while ($playlist = $select_bookmarks->fetch(PDO::FETCH_ASSOC)) {
        $course_id = $playlist['id'];
        ?>
        <div class="box">
            <div class="tutor">
                <img src="uploaded_files/<?= htmlspecialchars($playlist['tutor_image']); ?>" alt="">
                <div>
                    <h3><?= htmlspecialchars($playlist['tutor_name']); ?></h3>
                    <span><?= htmlspecialchars($playlist['date']); ?></span>
                </div>
            </div>
            <img src="uploaded_files/<?= htmlspecialchars($playlist['thumb']); ?>" class="thumb" alt="">
            <h3 class="title"><?= htmlspecialchars($playlist['title']); ?></h3>

            <!-- زر حفظ / إزالة الإشارة المرجعية -->
            <form method="post" class="save-list">
                <input type="hidden" name="list_id" value="<?= $course_id; ?>">
                <button type="submit" name="save_list">
                    <i class="fas fa-bookmark"></i>
                    <span data-ar="محفوظة" data-en="Saved"></span>
                </button>
            </form>

            <a href="playlist.php?get_id=<?= $course_id; ?>" class="inline-btn" data-ar="عرض قائمة التشغيل" data-en="View Playlist"></a>
        </div>
        <?php
    }
} else {
    echo '<p class="empty" data-ar="لا توجد اشارات مرجعية حتى الآن!" data-en="Nothing bookmarked yet!"></p>';
}
?>

</div>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>
