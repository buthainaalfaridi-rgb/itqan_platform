<?php
header('Content-Type: application/json');
echo json_encode([
    ['question' => 'Test question 1', 'option_1' => 'Option A', 'option_2' => 'Option B', 'option_3' => 'Option C', 'option_4' => 'Option D', 'correct_option' => '1'],
    // ... add more test questions ...
]);


if (isset($_GET['content_id'])) {
    $content_id = filter_var($_GET['content_id'], FILTER_SANITIZE_STRING);

    $get_questions = $conn->prepare("SELECT * FROM `quiz_questions` WHERE content_id = ?");
    $get_questions->execute([$content_id]);
    $questions = $get_questions->fetchAll(PDO::FETCH_ASSOC);

    // Check if questions were retrieved and encode them to JSON format
    if ($questions) {
        echo json_encode($questions);
    } else {
        echo json_encode([]); 
    }
} else {
    // Handle the case where content_id is not provided
    echo json_encode(["error" => "Content ID is missing"]);
}
?>