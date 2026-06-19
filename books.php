<?php
include 'components/connect.php';

// التحقق من تسجيل الدخول (بدون تحويل)
if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];
}else{
    $user_id = '';
}

// الحصول على الكتب حسب الفئة إذا تم اختيارها
$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title data-ar="الكتب" data-en="Books">الكتب</title>

<link rel="stylesheet" href="css/all.min.css">
<link id="style" rel="stylesheet" href="css/style-ar.css">
<link rel="stylesheet" href="css/login-overlay.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php if(!$user_id): ?>
<div id="login-overlay">
    <h2 data-ar="يجب عليك تسجيل الدخول للوصول إلى الكتب"
        data-en="You must log in to access the smart assistant">
        يجب عليك تسجيل الدخول للوصول إلى المساعد الذكي
    </h2>

    <button
        onclick="window.location.href='login.php'"
        data-ar="اذهب لتسجيل الدخول"
        data-en="Go to login">
        اذهب لتسجيل الدخول
    </button>
</div>
<?php endif; ?>

<!-- قسم الفئات -->
<section class="quick-select">
   <h1 class="heading" data-en="top categories" data-ar="أهم الفئات">أهم الفئات</h1>
   <div class="box-container">
      <div class="box">
         <div class="flex">
            <?php
            $select_categories = $conn->prepare("SELECT * FROM `categories`");
            $select_categories->execute();
            if($select_categories->rowCount() > 0){
                echo '<a href="books.php"><span data-en="all" data-ar="الكل">الكل</span></a>';
                while($cat = $select_categories->fetch(PDO::FETCH_ASSOC)){
                    echo '<a href="books.php?category_id='.$cat['id'].'">
                            <span data-en="'.htmlspecialchars($cat['name_en']).'"
                                  data-ar="'.htmlspecialchars($cat['name']).'">
                                  '.htmlspecialchars($cat['name']).'
                            </span>
                          </a>';
                }
            }
            ?>
         </div>
      </div>
   </div>
</section>

<!-- قسم الكتب -->
<section class="books">

<h1 class="heading">
<?php
if($category_id){
    $get_cat = $conn->prepare("SELECT name, name_en FROM `categories` WHERE id = ?");
    $get_cat->execute([$category_id]);
    if($get_cat->rowCount() > 0){
        $cat = $get_cat->fetch(PDO::FETCH_ASSOC);
        echo '<span data-en="Books in '.$cat['name_en'].'" data-ar="كتب في '.$cat['name'].'">كتب في '.$cat['name'].'</span>';
    }
}else{
    echo '<span data-en="Latest Books" data-ar="أحدث الكتب">أحدث الكتب</span>';
}
?>
</h1>

<form action="search_book.php" method="post" class="search-book">
    <input type="text" name="search_book" required
           data-ar="ابحث عن كتاب ..."
           data-en="Search book...">
    <button type="submit"></button>
</form>

<div class="box-container-book">
<?php
if($category_id){
    $select_books = $conn->prepare("SELECT * FROM `books` WHERE category_id = ?");
    $select_books->execute([$category_id]);
}else{
    $select_books = $conn->prepare("SELECT * FROM `books`");
    $select_books->execute();
}

if($select_books->rowCount() > 0){
    while($book = $select_books->fetch(PDO::FETCH_ASSOC)){
?>
    <div class="box-book">
        <img src="uploaded_files/<?= $book['book_image']; ?>" class="book-image">
        <h3><?= htmlspecialchars($book['title']); ?></h3>
        <p><?= htmlspecialchars($book['description']); ?></p>

        <div class="author-container">
            <p data-ar="الكاتب" data-en="Author"> <span><?= $book['author']; ?></span></p>
            <p data-ar="الصفحات" data-en="Pages"> <span><?= $book['num_pages']; ?></span></p>
        </div>

        <div class="btn-container-book">
            <a href="uploaded_files/<?= $book['book_file']; ?>" target="_blank"
               class="inline-btn" data-ar="قراءة" data-en="Read"></a>

            <a href="uploaded_files/<?= $book['book_file']; ?>" download
               class="inline-option-btn" data-ar="تحميل" data-en="Download"></a>
        </div>
    </div>
<?php
    }
}else{
    echo '<p class="empty" data-ar="لا توجد كتب" data-en="No books found"></p>';
}
?>
</div>

</section>

<?php include 'components/footer.php'; ?>

<script>
const userImageUrl = "<?php echo isset($imageUrl) ? $imageUrl : ''; ?>";
</script>
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>
</body>
</html>
