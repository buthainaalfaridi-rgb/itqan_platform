<?php
include 'components/connect.php';

if(!isset($_COOKIE['user_id'])){
   exit;
}

$user_id = $_COOKIE['user_id'];
$msg_id = $_POST['id'] ?? 0;

$delete = $conn->prepare("
   DELETE FROM messages
   WHERE id = ?
   AND sender_id = ?
   AND sender_type = 'student'
");

$delete->execute([$msg_id, $user_id]);

echo "success";