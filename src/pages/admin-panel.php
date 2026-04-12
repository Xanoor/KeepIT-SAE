<?php 
    session_start();

    // Only sys admin can access this page
    if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] != 'System Administrator') {
        header("Location: login.php");
        exit();
    }

    include_once("../includes/functions.php");

    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    unset($_SESSION['notification']);
    unset($_SESSION['notification_color']);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Panel Admin</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <?php include_once("../fragments/header.php"); ?>
        <main>
            <div class="page-name">
                <img alt="Logo du site" src="../assets/logo.png" />
                <h1>Panel Admin</h1>
            </div>
            
            <!-- Notification container -->
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
    </body>
    <!-- Scripts -->
    <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
    <script src="../scripts/notification.js"></script>
</html>
