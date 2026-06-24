<?php
    session_start();

    // Only the sys admin can access this action
    if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] !== 'System Administrator') {
        header("Location: ../pages/login.php");
        exit();
    }

    $flagFile = __DIR__ . '/../maintenance.flag';

    // We use a "flag file" to indicate if the maintenance is active or not
    if ($_POST['maintenance_action'] === 'on') {
        file_put_contents($flagFile, "Maintenance en cours"); // Create the flag file
    } elseif ($_POST['maintenance_action'] === 'off') {
        if (file_exists($flagFile)) {
            unlink($flagFile); // Delete the flag file
        }
    }

    header('Location: ../pages/admin-panel.php');
    exit;

?>