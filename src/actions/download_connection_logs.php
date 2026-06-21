<?php
session_start();

// Only sys admin can access this page
if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] != 'System Administrator') {
    header("Location: ../pages/login.php");
    exit();
}

include_once '../includes/functions.php';

if (isset($_POST['download_submit'], $_POST['type'])) {
    $type = $_POST['type'];
    if ($type === 'success') {
        $data = getSuccessConnectionLogsData();
        $filename = 'connexions_reussies.json';
    } elseif ($type === 'failed') {
        $data = getFailedConnectionLogsData();
        $filename = 'connexions_echouees.json';
    } elseif ($type === 'ssh') {
        $data = getSshLogsData();
        $filename = 'connexions_ssh.json';
    } else {
        header("Location: ../pages/admin-panel.php");
        exit();
    }

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo json_encode($data);
    exit();
}

header("Location: ../pages/admin-panel.php");
exit();
?>
