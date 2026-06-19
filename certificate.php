<?php
include 'components/connect.php';

$user_id = $_COOKIE['user_id'] ?? '';
if(!$user_id){
    header('location:login.php');
    exit;
}

/* 👤 المستخدم */
$stmt = $conn->prepare("SELECT name, image FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$user_name  = $user['name'] ?? 'Unknown';
$user_image = $user['image'] ?? 'default.png';

/* 📜 جلب الشهادة المطلوبة */

$playlist_id = $_GET['playlist_id'] ?? '';

if(!$playlist_id){
    die('معرف الدورة غير موجود');
}

$stmt = $conn->prepare("
    SELECT *
    FROM quiz_certificates
    WHERE student_id = ?
    AND playlist_id = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->execute([$user_id, $playlist_id]);

$certificate = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$certificate){
    die("لا توجد شهادة لهذه الدورة");
}

$playlist_id = $certificate['playlist_id'];
$percentage  = $certificate['score'];
$date        = $certificate['issued_at'];


/* 📚 الدورة */
$stmt = $conn->prepare("SELECT title FROM playlist WHERE id=?");
$stmt->execute([$playlist_id]);
$playlist_name = $stmt->fetchColumn() ?: '---';


/* 🏆 التقدير */
$grade_ar = match(true) {
    $percentage >= 90 => "ممتاز",
    $percentage >= 75 => "جيد جدًا",
    $percentage >= 50 => "جيد",
    default => "ضعيف"
};


/* 🆔 رقم الشهادة */
$cert_id = "CERT-" . strtoupper(substr(md5($certificate['id'] . $user_id), 0, 10));
?>

<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>شهادة إتمام</title>

<link rel="stylesheet" href="css/all.min.css">
<link id="style" rel="stylesheet" href="css/style-ar.css">
<script src="js/html2canvas.min.js"></script>
<script src="js/jspdf.umd.min.js"></script>
<style>

/* 🌐 الصفحة */
body{
    margin:0;
    font-family:'Cairo', sans-serif;
}

/* 🏆 wrapper */
.wrapper{
    display:flex;
    justify-content:center;
    padding:40px;
}

/* 📜 الشهادة */
.certificate{
    width: 900px;
    min-height: 1060px;
    background: #fff;
    border: 12px solid #0f172a;
    position: relative;
    text-align: center;
    padding: 50px 40px 220px;
    box-shadow: 0 20px 50px rgba(0,0,0,.15);
}

/* إطار داخلي */
.certificate::before{
    content:"";
    position:absolute;
    inset:18px;
    border:2px solid #3b82f6;
    pointer-events:none;
}

/* 🧾 رقم الشهادة */
.cert-id{
    position:absolute;
    top:20px;
    left:20px;
    font-size:12px;
    color:#666;
}

/* 🖼️ الشعار */
.logo{
    width:90px;
    margin-bottom:10px;
}

/* 🏆 العنوان */
h1{
    margin:0;
    font-size:44px;
    color:#0f172a;
}

h2{
    margin:5px 0 20px;
    font-size:20px;
    color:#666;
    font-weight:400;
}

/* 👤 صورة المستخدم */
.avatar{
    width:120px;
    height:120px;
    border-radius:50%;
    object-fit:cover;
    border:5px solid #3b82f6;
    margin:15px 0;
}

/* النصوص */
.text{
    font-size:18px;
    color:#444;
}

.name{
    font-size:34px;
    font-weight:bold;
    color:#2563eb;
    margin:15px 0;
}

.course{
    font-size:22px;
    color:#111;
    margin-top:10px;
}

/* النتيجة */
.score{
    margin-top:20px;
    font-size:18px;
}

.grade{
    font-size:18px;
    margin-top:5px;
    font-weight:bold;
    color:#16a34a;
}

/* التاريخ */
.date{
    margin-top:25px;
    font-size:14px;
    color:#666;
}

/* ✍️ footer */
.footer{
    background: #fff;
    position: absolute;
    bottom: 35px;
    left: 60px;
    right: 60px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.sign{
    width: 220px;
    text-align: center;
}

.sign::before{
    content: "";
    display: block;
    width: 180px;
    height: 1px;
    background: #cbd5e1;
    margin: 0 auto 12px;
}

.sign img{
    display: block;
    margin: 0 auto 10px;
    max-width: 140px;
    max-height: 70px;
    object-fit: contain;
}

.sign p{
    margin: 0;
    font-size: 14px;
    color: #444;
    font-weight: 600;
}

/* 🖨️ زر الطباعة */
.print-btn{
    display:block;
    margin:30px auto;
    padding:12px 30px;
    background:#3b82f6;
    color:#fff;
    border:none;
    border-radius:25px;
    cursor:pointer;
}

.print-btn:hover{
    background:#2563eb;
}

/* 🖨️ print */
@media print {

    @page{
        size: A4 portrait;
        margin: 0;
    }

    body{
        margin:0;
        padding:0;
        background:#fff;
    }

    .print-btn,
    .header{
        display:none !important;
    }

    .wrapper{
        padding:0;
        margin:0;
    }

    .certificate{
        width:210mm;
        height:297mm;
        margin:0;
        padding:25mm;
        box-shadow:none;
        overflow:hidden;
        page-break-inside:avoid;
    }

    .footer{
        position:absolute;
        bottom:25mm;
        left:25mm;
        right:25mm;
    }
}

</style>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<div class="wrapper">

<div class="certificate" id="printArea">

    <div class="cert-id"><?= $cert_id ?></div>

    <img src="images/logo-1.png" class="logo">

    <h1>Certificate</h1>
    <h2>Of Achievement</h2>

    <img src="uploaded_files/<?= htmlspecialchars($user_image) ?>" class="avatar">

    <p class="text">This is to certify that</p>

    <div class="name"><?= htmlspecialchars($user_name) ?></div>

    <p class="text">has successfully completed</p>

    <div class="course"><?= htmlspecialchars($playlist_name) ?></div>

    <div class="score">Score: <?= $percentage ?>%</div>

    <div class="grade">Grade: <?= $grade_ar ?></div>

    <div class="date"><?= $date ?></div>

    <div class="footer">
        <div class="sign">
            <img src="images/stamp.png">
            <p>Platform Seal</p>
        </div>

        <div class="sign">
            <img src="images/signature.png">
            <p>Instructor</p>
        </div>
    </div>

</div>
</div>

<button class="print-btn" onclick="window.print()">🖨 Print Certificate</button>

<?php include 'components/footer.php'; ?>
<script>
async function downloadPDF() {
    const { jsPDF } = window.jspdf;

    const element = document.getElementById("printArea");

    const canvas = await html2canvas(element, {
        scale: 2,
        useCORS: true
    });

    const imgData = canvas.toDataURL("image/png");

    const pdf = new jsPDF("p", "mm", "a4");

    const pageWidth = 210;
    const pageHeight = 297;

    const imgWidth = pageWidth;
    const imgHeight = (canvas.height * imgWidth) / canvas.width;

    let heightLeft = imgHeight;
    let position = 0;

    pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
    heightLeft -= pageHeight;

    while (heightLeft > 0) {
        position = heightLeft - imgHeight;
        pdf.addPage();
        pdf.addImage(imgData, "PNG", 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
    }

    pdf.save("certificate.pdf");
}
</script>
<script src="js/script.js"></script>
<script src="js/switcher.js"></script>

</body>
</html>