
<?php
include 'components/connect.php';

if (isset($_GET['mcq_id'])) {
    $mcq_id = $_GET['mcq_id'];
    $mcq_id = filter_var($mcq_id, FILTER_SANITIZE_NUMBER_INT);

    $select_options = $conn->prepare("SELECT * FROM `mcq_options` WHERE mcq_id = ?");
    $select_options->execute([$mcq_id]);
    $options = $select_options->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($options);
}
?>
