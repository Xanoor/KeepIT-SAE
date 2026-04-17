<?php

    session_start();

    // Everyone can access this page
    if (!isset($_SESSION['login'], $_SESSION['role'])) {
        header("Location: login.php");
        exit();
    }

    require_once '../includes/db.php';
    require_once '../includes/functions.php';

    $notification = $_SESSION['notification'] ?? null;
    $notification_color = $_SESSION['notification_color'] ?? null;
    $import_errors = $_SESSION['import_errors'] ?? null;
    unset($_SESSION['notification']);
    unset($_SESSION['notification_color']);
    unset($_SESSION['import_errors']);

    $techniciens = [];
    if (tableExists($connect, 'vw_dashboard_five_users_last_connection')) {
        $techResult = mysqli_query($connect, "SELECT * FROM vw_dashboard_five_users_last_connection");
        if ($techResult) {
            while ($row = mysqli_fetch_assoc($techResult)) {
                $techniciens[] = $row;
            }
        }
    }

    $inventaireItems = [];
    if (tableExists($connect, 'vw_dashboard_five_devices_last_update')) {
        $invResult = mysqli_query($connect, "SELECT * FROM vw_dashboard_five_devices_last_update");
        if ($invResult) {
            while ($row = mysqli_fetch_assoc($invResult)) {
                $inventaireItems[] = $row;
            }
        }
    }

    $displayName = htmlspecialchars($_SESSION['login']);
    if (!empty($_SESSION['first_name']) || !empty($_SESSION['last_name'])) {
        $displayName = htmlspecialchars(trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')));
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Statistics - KEEPIT</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="../styles/global.css" />
    <link rel="stylesheet" type="text/css" href="../styles/inventory-table.css" />
    <link rel="stylesheet" type="text/css" href="../styles/dashboard.css" />
    <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
</head>
<body>
    <?php include_once("../fragments/header.php"); ?>

    

    <main class="dashboard-main">
        <div class="back-dashboard">
            <a href="index.php" class="more-stats">Retour vers le dashboard</a>
        </div>
        <div class="dashboard-charts">
            <div class="chart-card">
                <p class="chart-title">Temps de connexion des utilisateurs</p>
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-connexion" aria-label="Graphique : temps de connexion"></canvas>
                </div>
            </div>
            
            <div class="chart-card">
                <p class="chart-title">Capacités des UC</p>
                
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-spec-minimum" aria-label="Graphique : Capacite du parc info"></canvas>
                </div>
                
                <div class="chart-settings-container">
                    <div class="setting-item">
                        <label for="select-memory">RAM Minimum (Mo)</label>
                        <select id="select-memory" class="select-chart">
                            <option value="4096">4096</option>
                            <option value="8192">8192</option>
                            <option value="16384">16384</option>
                            <option value="32768">32768</option>
                        </select>
                    </div>

                    <div class="setting-item">
                        <label for="select-disk">Stockage minimum (Go)</label>
                        <select id="select-disk" class="select-chart">
                            <option value="256">256</option>
                            <option value="512">512</option>
                            <option value="1024">1024</option>
                        </select>
                    </div>

                    <div class="setting-item">
                        <label for="select-city">Site</label>
                        <select id="select-city" class="select-chart">
                            <option value="Vélizy">Vélizy</option>
                            <option value="Rambouillet">Rambouillet</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <p class="chart-title">Incohérence entre capacité de l'uc et taille de l'écran associé</p>
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-incoherence" aria-label="Graphique : Incohérence entre uc et écran"></canvas>
                </div>
            </div>

            <div class="chart-card full-width">
                <p class="chart-title">Prévisions des fins de garanties en fonction du constructeur</p>
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-warranties" aria-label="Graphique : Prévisions des fins de garanties"></canvas>
                </div>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script src="../scripts/dashboard.js"></script>
    <!-- Notification container -->
        <div class="notifications-container" id="notificationsContainer"></div>
    </body>
    <script>const notif = <?= json_encode($notification) ?>;const notif_color = <?= json_encode($notification_color) ?>;</script>
    <script src="../scripts/notification.js"></script>
</html>