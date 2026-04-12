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
    <title>Dashboard - KEEPIT</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="../styles/global.css" />
    <link rel="stylesheet" type="text/css" href="../styles/inventory-table.css" />
    <link rel="stylesheet" type="text/css" href="../styles/dashboard.css" />
    <link rel="stylesheet" type="text/css" href="../styles/notification.css" />
</head>
<body>
    <?php include_once("../fragments/header.php"); ?>

    <main class="dashboard-main">

        <!-- Page title -->
        <div class="page-name">
            <img alt="Logo du site" src="../assets/logo.png" />
            <h1>Dashboard</h1>
        </div>

        <div class="dashboard-grid">

            <!-- Techniciens -->
            <div class="dashboard-panel">
                <div class="dashboard-panel-header">
                    <h2>Techniciens</h2>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Dernière connexion</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($techniciens)): ?>
                            <tr class="placeholder-row">
                                <td colspan="4">Aucun technicien trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($techniciens as $tech): ?>
                                <tr>
                                    <td class="col-id"><?= htmlspecialchars($tech['last_name'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($tech['first_name'] ?? '—') ?></td>
                                    <td>
                                        <?php
                                            $label = timeAgoFr($tech['last_login_at']);
                                            $class = ($label === 'Maintenant') ? ' class="last-login-now"' : '';
                                            echo "<span{$class}>" . htmlspecialchars($label) . "</span>";
                                        ?>
                                    </td>
                                    <td class="col-action">
                                        <a href="#" title="Voir le profil">
                                            <img src="../assets/open.png" alt="Ouvrir" />
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Inventaire -->
            <div class="dashboard-panel">
                <div class="dashboard-panel-header">
                    <h2>Inventaire</h2>
                </div>
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>N° de série</th>
                            <th>Nom de l'appareil</th>
                            <th>Catégorie</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventaireItems)): ?>
                            <tr class="placeholder-row">
                                <td colspan="5">Aucun appareil trouvé</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($inventaireItems as $item): ?>
                                <tr>
                                    <td class="col-id"><?= htmlspecialchars($item['serial_number']) ?></td>
                                    <td><?= htmlspecialchars($item['display_name'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($item['device_type'] ?? '—') ?></td>
                                    <td>
                                        <span class="<?= getStateClass($item['state']) ?>">
                                            <?= htmlspecialchars($item['state']) ?>
                                        </span>
                                    </td>
                                    <td class="col-action">
                                        <a href="inventory-item.php?serialNumber=<?= urlencode($item['serial_number']) ?>&deviceType=<?= urlencode($item['device_type']) ?>"
                                           title="Voir l'appareil">
                                            <img src="../assets/open.png" alt="Ouvrir" />
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts -->
        <div class="dashboard-charts">

            <div class="chart-card">
                <p class="chart-title">Appareils par type</p>
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-devices-by-type" aria-label="Graphique : appareils par type"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <p class="chart-title">Répartition des statuts</p>
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-states" aria-label="Graphique : répartition des statuts"></canvas>
                </div>
            </div>

            <div class="chart-card">
                <p class="chart-title">Activité récente (14 j.)</p>
                <div class="chart-canvas-wrapper">
                    <canvas id="chart-activity" aria-label="Graphique : activité récente"></canvas>
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