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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Library</title>
    <link rel="stylesheet" href="css/style-ar.css"> </head>
<body>

<section class="books">
    <h1 class="heading">Book Library</h1>

    <div class="box-container">
        <?php
            $select_books = $conn->prepare("SELECT * FROM `books` WHERE status = ? ORDER BY date DESC");
            $select_books->execute(['active']);
            if($select_books->rowCount() > 0){
                while($fetch_book = $select_books->fetch(PDO::FETCH_ASSOC)){
                    $book_id = $fetch_book['id'];
        ?>
        <div class="box">
            <img src="uploaded_files/<?= $fetch_book['image']; ?>" class="book-image" alt="<?= $fetch_book['title']; ?>">
            <h3 class="title"><?= $fetch_book['title']; ?></h3>
            <p class="description"><?= $fetch_book['description']; ?></p>
            <a href="book.php?get_id=<?= $book_id; ?>" class="inline-btn">View Book</a>
            <a href="uploaded_files/<?= $fetch_book['file']; ?>" download class="inline-btn">Download</a> 
        </div>
        <?php
                }
            }else{
                echo '<p class="empty">No books added yet!</p>';
            }
        ?>
    </div>
</section>

<section class="video-form">
    <h1 class="heading">Upload Book</h1>

    <form action="" method="post" enctype="multipart/form-data">
        <p>Book Status <span>*</span></p>
        <select name="status" class="box" required>
            <option value="" selected disabled>-- select status</option>
            <option value="active">active</option>
            <option value="inactive">inactive</option>
        </select>
        <p>Book Title <span>*</span></p>
        <input type="text" name="title" maxlength="100" required placeholder="enter book title" class="box">
        <p>Book Description <span>*</span></p>
        <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
        <p>Select Cover Image <span>*</span></p>
        <input type="file" name="thumb" accept="image/*" required class="box">
        <p>Select Book File <span>*</span></p>
        <input type="file" name="book" accept="application/pdf,application/epub" required class="box">
        <input type="submit" value="Upload Book" name="submit" class="btn">
    </form>

    <?php
    if(isset($_POST['submit'])){
        $status = $_POST['status'];
        $title = $_POST['title'];
        $description = $_POST['description'];
        $thumb_name = $_FILES['thumb']['name'];
        $thumb_tmp_name = $_FILES['thumb']['tmp_name'];
        $thumb_folder = 'uploaded_files/'.$thumb_name;
        $book_name = $_FILES['book']['name'];
        $book_tmp_name = $_FILES['book']['tmp_name'];
        $book_folder = 'uploaded_files/'.$book_name;

        // Insert data to database
        $insert_book = $conn->prepare("INSERT INTO `books` (title, description, image, file, status) VALUES(?,?,?,?,?)");
        $insert_book->execute([$title, $description, $thumb_folder, $book_folder, $status]);

        // Move uploaded files to the designated folder
        if(move_uploaded_file($thumb_tmp_name, $thumb_folder) && move_uploaded_file($book_tmp_name, $book_folder)){
            $message[] = 'Book uploaded successfully!';
        }else{
            $message[] = 'Could not upload book!';
        }

    }
    ?>
</section>

<script src="script.js"></script>
</body>
</html>