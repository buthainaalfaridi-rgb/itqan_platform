
<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];

    // Delete all chats from the database
    $query = $conn->prepare("DELETE FROM user_chats WHERE user_id = ?");
    $query->execute([$user_id]);
}

?>
