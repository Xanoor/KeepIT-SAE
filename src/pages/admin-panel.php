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
        <link rel="stylesheet" type="text/css" href="../styles/admin.css" />
        <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
    </head>
    <body>
        <?php include_once("../fragments/header.php"); ?>
        <main class="panel-main">
            <div class="page-name">
                <img alt="Logo du site" src="../assets/logo.png" />
                <h1>Panel Admin</h1>
            </div>
            <section class="maintenance-section">
                <p>Mode maintenance</p>
                <form method="post" action="../actions/maintenance_action.php">
                    <button name="maintenance_action" value="<?= isMaintenanceActive() ? 'off' : 'on' ?>" class="maintenance_button<?= isMaintenanceActive() ? ' maintenance_button_on' : '' ?>">
                        <?= isMaintenanceActive() ? 'Maintenance Activée (Désactiver)' : 'Maintenance Désactivée (Activer)' ?>
                    </button>
                </form>
            </section>
            <div class="logs-container">
                <section class="log-section">
                    <div class="section-header">Logs utilisateurs</div>
                    <div class="sys-logs subsections">
                        <?php echo loadUsersLogs() ?>
                    </div>
                </section>

                <section class="log-section">
                    <div class="section-header">Logs de constantes</div>
                    <div class="sys-logs subsections">
                        <?php echo loadConstantLogs() ?>
                    </div>
                </section>

                <section class="log-section">
                    <div class="section-header">
                        <span>Connexions réussies</span>
                        <form action="../actions/download_connection_logs.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="type" value="success">
                            <button type="submit" name="download_submit" class="save-json-btn">Enregistrer JSON</button>
                        </form>
                    </div>
                    <div class="sys-logs subsections">
                        <?php echo loadConnectionLogs(1) ?>
                    </div>
                </section>

                <section class="log-section">
                    <div class="section-header">
                        <span>Connexions échouées</span>
                        <form action="../actions/download_connection_logs.php" method="POST" style="margin: 0;">
                            <input type="hidden" name="type" value="failed">
                            <button type="submit" name="download_submit" class="save-json-btn">Enregistrer JSON</button>
                        </form>
                    </div>
                    <div class="sys-logs subsections">
                        <?php echo loadConnectionLogs(0) ?>
                    </div>
                </section>
            </div>
        
            <!-- Notification container -->
            <div class="notifications-container" id="notificationsContainer"></div>
        </main>
    </body>
    <!-- Scripts -->
    <script>
        const notif = <?= json_encode($notification) ?>;
        const notif_color = <?= json_encode($notification_color) ?>;
    </script>
    <script src="../scripts/notification.js"></script>
</html>
