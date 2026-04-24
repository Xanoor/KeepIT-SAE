<?php 
    session_start();

    // Only web admin can access this page
    if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] != 'Web Administrator') {
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
        <title>Paramètres du site</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <?php include_once("../fragments/header.php"); ?>
        <main class="panel-main">
            <div class="page-name">
                <img alt="Logo du site" src="../assets/logo.png" />
                <h1>Paramètres du site</h1>
            </div>
            <section class="web-variables">
                <?php
                    createVariableConfig("locations", "location");
                    createVariableConfig("manufacturer", "name");
                    createVariableConfig("operating_system", "name");
                    createVariableConfig("connector", "name");
                ?>
            </section>
            <section class="web-administration">
                <form action="../actions/admin-settings_action.php" method="POST">
                    <div class="ban-ip-class">
                        <label for="IP_ADDR">Bannir une adresse IPv4</label>
                        <input type="text" name="IP_ADDR" placeholder="Adresse IPv4" id="IP_ADDR">
                        <input type="submit" name="BAN_IP_SUBMIT" value="Bannir">
                    </div>
                    <div class="banned-ip-class">
                        <!-- TODO: create the functions that display banned ip with unban btn, waiting for DB part -->
                    </div>
                </form>
            </section>
        
            <!-- Notification container -->
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
    </body>
    <!-- Scripts -->
    <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
    <script src="../scripts/notification.js"></script>
</html>
