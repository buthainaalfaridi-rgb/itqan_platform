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
    <title data-ar="البحث عن كتاب" data-en="Search for a book"></title>

    <!-- font awesome cdn link  -->
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"> -->

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/all.min.css">
    <link id="style" rel="stylesheet" href="css/style-ar.css">

</head>

<body>

<?php include 'components/user_header.php'; ?>

<section class="books">

    <h1 class="heading" data-ar="الكتب" data-en="Books"></h1>
    <form action="" method="post" class="search-book">
        <input type="text" name="search_book" maxlength="100" data-ar="ابحث عن كتاب ..." data-en="search book..." required>
        <button type="submit" name="search_book_btn" class="fas fa-search"></button>
    </form>
    <div class="box-container-book">

    <?php
    if(isset($_POST['search_book']) || isset($_POST['search_book_btn'])){
        $search_book = $_POST['search_book'];
        $select_books = $conn->prepare("SELECT * FROM `books` WHERE title LIKE ?");
        $select_books->execute(['%' . $search_book . '%']);
        if($select_books->rowCount() > 0){
            while($fetch_book = $select_books->fetch(PDO::FETCH_ASSOC)){
                $book_id = $fetch_book['id'];
    ?>
    <div class="box-container-book">
        <div class="box-book">
            <img src="uploaded_files/<?= htmlspecialchars($fetch_book['book_image']); ?>" class="book-image" alt="<?= htmlspecialchars($fetch_book['title']); ?>">
            <h3 class="title-book"><?= htmlspecialchars_decode($fetch_book['title']); ?></h3>
            <p class="description-book"><?= htmlspecialchars_decode($fetch_book['description']); ?></p>
            <div class="author-container">
                <p class="author-book" data-ar="الكاتب" data-en="Author"> <span><?= htmlspecialchars_decode($fetch_book['author']); ?></span></p>
                <p class="page-number-book"  data-ar="الصفحات" data-en="Pages"> <span><?= htmlspecialchars($fetch_book['num_pages']); ?></span></p>
            </div>
            <div class="btn-container-book">
                <a href="uploaded_files/<?= htmlspecialchars($fetch_book['book_file']); ?>" target="_blank" class="inline-btn" data-ar="قراءة" data-en="Read"></a>
                <a href="uploaded_files/<?= htmlspecialchars($fetch_book['book_file']); ?>" download="<?= htmlspecialchars_decode($fetch_book['title']); ?>.pdf" class="inline-option-btn" data-ar="تحميل" data-en="Download"></a>
            </div>
        </div>
    </div>
    <?php
            }
        } else {
            echo '<p class="empty" data-ar="لم يتم العثور على نتائج!" data-en="no results found!"></p>';
        }
    } else {
        echo '<p class="empty" data-ar="يرجى البحث عن شيء!" data-en="please search something!"></p>';
    }
    ?>
    </div>
</section>
<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>
