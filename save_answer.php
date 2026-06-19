<?php
include 'components/connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$student_id = $_COOKIE['user_id'] ?? null;

if(!$student_id || !$data){
    echo "error";
    exit;
}

$question_id = $data['question_id'];
$selected_option = $data['selected_option_id'];
$playlist_id = $data['playlist_id'];

/* 🔥 نجيب الإجابة الصحيحة من قاعدة البيانات */
$stmt = $conn->prepare("SELECT answer FROM playlist_questions WHERE id = ?");
$stmt->execute([$question_id]);
$correct_answer = $stmt->fetchColumn();

/* هل الإجابة صحيحة؟ */
$is_correct = ($selected_option == $correct_answer) ? 1 : 0;

/* 🔥 تحقق إذا سبق الإجابة */
$check = $conn->prepare("
    SELECT id FROM quiz_answers 
    WHERE student_id = ? AND question_id = ?
");
$check->execute([$student_id, $question_id]);
$exists = $check->fetchColumn();

if($exists){
    $update = $conn->prepare("
        UPDATE quiz_answers 
        SET selected_option = ?,
            is_correct = ?,
            answered_at = NOW(),
            playlist_id = ?
        WHERE student_id = ? AND question_id = ?
    ");
    $update->execute([
        $selected_option,
        $is_correct,
        $playlist_id,
        $student_id,
        $question_id
    ]);
} else {
    $insert = $conn->prepare("
        INSERT INTO quiz_answers 
        (student_id, question_id, selected_option, is_correct, answered_at, playlist_id)
        VALUES (?, ?, ?, ?, NOW(), ?)
    ");
    $insert->execute([
        $student_id,
        $question_id,
        $selected_option,
        $is_correct,
        $playlist_id
    ]);
}

echo "ok";