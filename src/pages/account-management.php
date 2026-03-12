<?php 
    session_start();

    // Everyone that have a role (tech, adm...) can access this page
    if (!isset($_SESSION['login'])) {
        header("Location: login.php");
        exit();
    }

    include_once("../includes/functions.php"); 
    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    unset($_SESSION['notification']);
    unset($_SESSION['notification_color']);

    // These default logins cannot be edited
    if ($_SESSION['login'] === "tech1" || $_SESSION['login'] === "sysadmin" || $_SESSION['login'] === "adminweb") {
        $_SESSION['notification'] = "Vous ne pouvez pas modifier les informations de ce compte.";
        $_SESSION['notification_color'] = "red";
        header("Location: index.php");
        exit();

    }

    // Searching the values of the user in the sql database.
    require_once '../includes/db.php';
        
    $db = $GLOBALS['connect'];
    $login = $_SESSION['login'];
    $sql = "SELECT first_name, last_name, role, password_hash FROM users WHERE login = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "s", $login);
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        $resultat = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['notification'] = "Erreur lors de la récupération des informations de votre compte.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/index.php");
        exit();
    }

    ?>



<!doctype html>
<html lang="fr">
    <head>
        <title>Gestion du compte</title>
        <meta charset="UTF-8" />
        <link rel="stylesheet" type="text/css" href="../styles/global.css" />
        <link rel="stylesheet" type="text/css" href="../styles/account-management.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <header>
            <div class="nav-left-container">
                <div class="app-name-container">
                    <a href="index.php">KEEPIT</a>
                </div>
                <nav class="nav-buttons-container">
                    <a href="index.php">Dashboard</a>
                    <a href="inventory.php">Inventaire</a>
                    <a href="technician.php">Techniciens</a>
                    <a href="#">Informations</a>
                </nav>
            </div>
            <nav class="nav-right-container">
                <a href="#" class="profile-btn-selected">Profil</a>
                <a href="../actions/logout_action.php" class="log-out"
                    ><img src="../assets/log-out.png" alt="Deconnexion"
                /></a>
            </nav>
        </header>
        <main class="account-management">
    <div class="page-name">
        <img alt="Logo du site" src="../assets/logo.png" />
        <h1>Mon compte</h1>
    </div>

    <form id="update-account-form" method="post" action="../actions/account-management_action.php">
        <div class="account-container">
            
            <div class="account-card">
                <h2>Informations personnelles</h2>
                <div class="form-group">
                    <label for="first-name">Prénom</label>
                    <input type="text" id="first-name" name="first-name" value="<?= htmlspecialchars($resultat['first_name']) ?>" />

                    <label for="last-name">Nom</label>
                    <input type="text" id="last-name" name="last-name" value="<?= htmlspecialchars($resultat['last_name']) ?>" />

                    <div class="input-readonly-container">
                        <label for="job">Fonction</label>
                        <input type="text" id="job" name="job" value="<?= htmlspecialchars(convertDataToFrench($resultat['role'])) ?>" readonly />
                        <span class="tooltip">Vous n'avez pas la permission de changer de fonction.</span>
                    </div>
                </div>
            </div>

            <div class="account-card">
                <h2>Identifiants de connexion</h2>
                <div class="form-group">
                    <div class="input-readonly-container">
                        <label for="login">Login</label>
                        <input type="text" id="login" name="login" value="<?= htmlspecialchars($_SESSION['login']) ?>" readonly />
                        <span class="tooltip">Il est impossible de changer de login.</span>
                    </div>

                    <label for="password">Mot de passe</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" placeholder="*******" />
                        <span class="eye-icon"></span>
                    </div>
                </div>
            </div>
        </div>
        <input type="submit" name="submit" value="Enregistrer" >
    </form>
    </main>
    <!-- Notification container -->
    <div class="notifications-container" id="notificationsContainer"></div>
    </body>
    <script src="../scripts/showPassword.js"></script>
    <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
    <script src="../scripts/notification.js"></script>
</html>
