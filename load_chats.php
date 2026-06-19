<?php
include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
    $user_id = $_COOKIE['user_id'];

    // Fetch the user's image from the 'users' table
    $user_query = $conn->prepare("SELECT image FROM users WHERE id = ?");
    $user_query->execute([$user_id]);
    $user = $user_query->fetch(PDO::FETCH_ASSOC);
    $user_image = $user ? 'uploaded_files/' . $user['image'] : 'images/default_user.png';

    // Fetch all chats for the current user
    $query = $conn->prepare("SELECT message, message_type FROM user_chats WHERE user_id = ? ORDER BY timestamp ASC");
    $query->execute([$user_id]);

    $chats = $query->fetchAll(PDO::FETCH_ASSOC);

    // Generate HTML for each chat
    foreach ($chats as $chat) {
        $messageType = $chat['message_type'];
        $messageContent = html_entity_decode($chat['message'], ENT_QUOTES, 'UTF-8'); // Decode the stored message

        if ($messageType === 'outgoing') {
            // Outgoing message (User)
            $imageSrc = $user_image;
            $altText = 'User Image';
            $classes = 'outgoing';
            $iconHtml = ''; // No copy icon for outgoing messages
        } else {
            // Incoming message (AI)
            $imageSrc = 'images/gemini.svg';
            $altText = 'Gemini Image';
            $classes = 'incoming';
            $iconHtml = '<span onclick="copyMessage(this)" class="icon material-symbols-rounded">content_copy</span>';
        }

        echo '<div class="g-message ' . $classes . '">';
        echo '  <div class="g-message-content">';
        echo '      <img src="' . $imageSrc . '" alt="' . $altText . '" class="avatar">';
        echo '      <p class="text">' . $messageContent . '</p>'; // Render the decoded message
        echo '  </div>';
        echo $iconHtml; // Include the copy icon only for AI messages
        echo '</div>';
    }
}
?>
