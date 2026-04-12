<?php 
    session_start();
    if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Web Administrator') {
        header("Location: technician.php");
        exit();
    }
    
    include_once("../includes/functions.php");

    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    unset($_SESSION['notification'], $_SESSION['notification_color']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>KEEPIT - Créer un Technicien</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="../styles/global.css" />
    <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    <link rel="stylesheet" type="text/css" href="../styles/technician.css" />
</head>
<body>
    <?php include_once("../fragments/header.php"); ?>
    <main>
        <div class="page-name">
            <img alt="Logo" src="../assets/logo.png" />
            <h1>Nouveau Technicien</h1>
        </div>

        <section class="edit-tech-footer">
            <h2 class="edit-tech-title">Informations du compte</h2>
            
            <form class="edit-form-section" action="../actions/create_technician_action.php" method="POST">
                <div class="form-group">
                    <label for="first_name">Prénom</label>
                    <input type="text" name="first_name" id="first_name" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Nom</label>
                    <input type="text" name="last_name" id="last_name" required>
                </div>
                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" name="login" id="login" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe (provisoire)</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" placeholder="****************" required>
                        <span class="eye-icon"></span>
                    </div>
                </div>
                
                <div class="form-actions-row">
                    <button type="submit" class="btn-save-edit">Créer le compte</button>
                    <a href="technician.php" class="btn-cancel-creation">
                        Annuler
                    </a>
                </div>
            </form>
        </section>
        <div class="notifications-container" id="notificationsContainer"></div>
    </main>
</body>
<script src="../scripts/showPassword.js"></script>
<script>
        const notif = <?= json_encode($notification) ?>;
        const notif_color = <?= json_encode($notification_color) ?>;
    </script>
    <script src="../scripts/notification.js"></script>
</html>