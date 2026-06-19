<?php
include 'components/connect.php';

// Read the JSON input
$input = json_decode(file_get_contents("php://input"), true);

if(isset($_COOKIE['user_id']) && isset($input['message']) && isset($input['message_type'])){
    $user_id = $_COOKIE['user_id'];
    $message = htmlentities($input['message'], ENT_QUOTES, 'UTF-8'); // Encode the message
    $message_type = $input['message_type'];

    // Insert the chat into the database
    $query = $conn->prepare("INSERT INTO user_chats (user_id, message, message_type) VALUES (?, ?, ?)");
    $query->execute([$user_id, $message, $message_type]);
}
?>
