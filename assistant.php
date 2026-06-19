<?php
include 'components/connect.php';

// تحقق من تسجيل الدخول
$user_id = null;
if(isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id'])){
    $temp_id = $_COOKIE['user_id'];
    $select_profile = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $select_profile->execute([$temp_id]);

    if($select_profile->rowCount() > 0){
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        $imageUrl = 'uploaded_files/' . $fetch_profile['image'];
        $user_id = $temp_id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-ar="مساعدك الذكي" data-en="Smart Assistant"></title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="css/all.min.css">
    <link id="style" rel="stylesheet" href="css/style-en.css">

    <script src="lib/marked.min.js"></script>
    <script src="lib/purify.min.js"></script>

    
</head>
<body class="g-body">
<link rel="stylesheet" href="css/login-overlay.css">

<?php include 'components/user_header.php'; ?>

<header class="g-header">
    <h2 class="g-title" data-ar="أنا مساعدك الذكي" data-en="I'm your Smart Assistant"></h2>
    <h4 class="g-subtitle" data-ar="كيف يمكنني مساعدتك اليوم؟" data-en="How I can help you today?"></h4>
</header>

<div class="chat-list"></div>

<div class="typing-area">
    <form action="#" class="typing-form">
        <div class="input-wrapper">
            <input type="text" class="typing-input" data-ar="أدخل طلبك هنا" data-en="Enter your prompt here" <?php echo $user_id ? '' : 'disabled'; ?> required>
            <button class="icon material-symbols-rounded" <?php echo $user_id ? '' : 'disabled'; ?>>send</button>
        </div>
        <div class="action-buttons">
            <span id="delete-chat-button" class="icon material-symbols-rounded">delete</span>
        </div>
    </form>
    <p class="disclaimer-text" data-ar="قد يعرض المساعد الذكي معلومات غير دقيقة، بما في ذلك معلومات عن الأشخاص، لذا تحقق جيدًا من إجاباته." 
       data-en="Smart Assistant may display inaccurate info, including about people, so double-check its responses."></p>
</div>

<!-- Overlay رسالة تسجيل الدخول -->
<?php if(!$user_id): ?>
<div id="login-overlay">
    <h2 data-ar="يجب عليك تسجيل الدخول للوصول إلى المساعد الذكي"
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

<script>
const userImageUrl = "<?php echo isset($imageUrl) ? $imageUrl : ''; ?>";
</script>

<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>
