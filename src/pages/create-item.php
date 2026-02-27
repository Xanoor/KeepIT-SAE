<?php
session_start();

// Everyone that have a role (tech, adm...) can access this page
if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

include_once("../includes/functions.php");

$notification = $_SESSION['notification'] ?? null;
$notification_color = $_SESSION['notification_color'] ?? null;
$import_errors = $_SESSION['import_errors'] ?? null;
unset($_SESSION['notification']);
unset($_SESSION['notification_color']);
unset($_SESSION['import_errors']);
?>

<!doctype html>
<html lang="fr">
    <head>
        <title>Page de configuration</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
        <link
            rel="stylesheet"
            type="text/css"
            href="../styles/inventory-item.css"
        />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="index.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="index.php">Dashboard</a>
                    <a href="inventory.php" class="nav-buttons-current"
                        >Inventaire</a
                    >
                    <a href="#">Techniciens</a>
                    <a href="#">Informations</a>
                </nav>
            </div>
            <nav class="nav-right-container">
                <a href="#" class="profile-btn">Profil</a>
                <a href="../actions/logout_action.php" class="log-out">
                    <img src="../assets/log-out.png" alt="Déconnexion"/>
                </a>
            </nav>
        </header>
        <main class="inventory-item-main">
            <div>
                <div class="page-name">
                    <img alt="Logo du site" src="../assets/logo.png" />
                    <h1>Inventaire</h1>
                </div>
                <div class="create-item-header">
                    <select
                        name="device_type"
                        id="create-item-select"
                        aria-label="Type d'appareil"
                    >
                        <option disabled selected hidden>
                            Selectionner le type
                        </option>
                        <option value="COMPUTER">Ordinateur</option>
                        <option value="MONITOR">Écran</option>
                    </select>
                </div>

                <section class="inventory-item-main-section">
                    <form action="../actions/create-item_action.php" method="POST" id="create-item-form">
                       
                    </form>
                </section>
            </div>
        </main>
        <!-- Notification container -->
        <div class="notifications-container" id="notificationsContainer"></div>
    </body>
    <script>
        const notif = <?= json_encode($notification) ?>;
        const notif_color = <?= json_encode($notification_color) ?>;
    </script>
    <script src="../scripts/notification.js"></script>
    <script src="../scripts/callItemForm.js"></script>
</html>
