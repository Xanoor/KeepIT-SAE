<?php
    require_once '../includes/maintenance-fnc.php';

    if (session_status() === PHP_SESSION_NONE) { 
        session_start(); 
    } 

    // If maintenance is active, users are allowed to stay on the login page to switch to an admin account.
    // or if the maintenance is inactive, go back to index.php
    if ((isMaintenanceActive() && canBypassMaintenance()) || !isMaintenanceActive()) {
        header("Location: index.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Maintenance</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="../styles/global.css" />
    <link rel="stylesheet" type="text/css" href="../styles/maintenance.css" />
</head>
<body>
    <main>
        <div id="maintenance-header">
            <img alt="Logo du site" src="../assets/logo.png" />
            <h1>KEEPIT</h1>
        </div>

        <h2>Maintenance</h2>

        <p class="maintenance-message">
            Le site est actuellement en maintenance. Seuls les administrateurs peuvent se connecter.
        </p>
        <a href="../actions/logout_action.php" class="logout-btn">Se déconnecter</a>
    </main>
</body>
</html>