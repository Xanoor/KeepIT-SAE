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
        <link rel="stylesheet" type="text/css" href="../styles/admin.css" />
    </head>
    <body>
        <?php include_once("../fragments/header.php"); ?>
        <main class="panel-main">
            <div class="page-name">
                <img alt="Logo du site" src="../assets/logo.png" />
                <h1>Paramètres du site</h1>
            </div>
            <section class="web-variables">
                <h2>Gestion des variables</h2>
                <div class="web-variables-form">
                    <?php
                        createVariableConfig("locations", "location");
                        createVariableConfig("manufacturer", "name");
                        createVariableConfig("operating_system", "name");
                        createVariableConfig("connector", "name");
                        createVariableConfig("device_states", "state");
                    ?>
                </div>
                <div class="create-web-variables">
                    <input type="button" id="CREATE_VAR_BTN" name="NEW_VAR_FORM" value="Créer une nouvelle variable">
                </div>
            </section>
            <section class="web-administration">
                <h2>Administration</h2>
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
            <div class="createVariableModal" id="createVariableModal">
                <form action="../actions/admin-settings_action.php" method="POST">
                    <button type="button" id="close-modal">X</button>

                    <h3>Ajouter une variable</h3>
                    <label for="TABLE_SELECT">Variable:</label>
                    <select id="TABLE_SELECT" name="table-select">
                        <option value="connector">Connecteurs</option>
                        <option value="locations">Localisations</option>
                        <option value="operating_system">Systèmes d'exploitations</option>
                        <option value="manufacturer">Fabricants</option>
                        <option value="device_states">Statut</option>
                    </select>

                    <label for="VARIABLE_VALUE_INPUT">Valeur:</label>
                    <input type="text" name="var-value" id="VARIABLE_VALUE_INPUT" placeholder="Valeur de la variable">

                    <input type="submit" name="CREATE_VAR" value="Ajouter">
                </form>
            </div>
            <!-- Notification container -->
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
    </body>
    <!-- Scripts -->
    <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
    <script src="../scripts/notification.js"></script>
    <script src="../scripts/admin.js"></script>
</html>
