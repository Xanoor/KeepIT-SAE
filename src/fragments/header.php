<?php
    // Get the current page name without the path
    $currentPageName = basename($_SERVER['PHP_SELF']);

    $displayName = htmlspecialchars($_SESSION['login'] ?? 'Profil');
    if (!empty($_SESSION['first_name']) || !empty($_SESSION['last_name'])) {
        $displayName = htmlspecialchars(trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')));
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../includes/maintenance-fnc.php';
    $test = checkMaintenance();
    echo $test;

?>
<header>
    <div class="nav-left-container">
        <div class="app-name-container">
            <a href="index.php">KEEPIT</a>
        </div>
        <nav class="nav-buttons-container">
            <!-- Global header (all roles) -->
            <a href="index.php" class="<?= ($currentPageName == (in_array($currentPageName, ['index.php', 'statistics.php']))) ? 'nav-buttons-current' : '' ?>">Dashboard</a>

            <!-- Tech and admin web only -->
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] == "Web Administrator" || $_SESSION['role'] == "Technician")): ?>
                <a href="inventory.php" class="<?= (in_array($currentPageName, ['inventory.php', 'inventory-item.php', 'create-item.php', 'importCSV.php'])) ? 'nav-buttons-current' : '' ?>">Inventaire</a>
                <a href="team_infos.php" class="<?=  (in_array($currentPageName, ['team_infos.php']) ? 'nav-buttons-current' : '') ?>">L'équipe</a>
                <a href="rgpd_table.php" class="<?=  (in_array($currentPageName, ['rgpd_table.php']) ? 'nav-buttons-current' : '') ?>">RGPD</a>
            <?php endif; ?>

            <!-- Admin web only -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Web Administrator'): ?>
                <a href="technician.php" class="<?= (in_array($currentPageName, ['technician.php', 'create-technician.php'])) ? 'nav-buttons-current' : '' ?>">Techniciens</a>
                <a href="admin-settings.php" class="<?= (in_array($currentPageName, ['admin-settings.php'])) ? 'nav-buttons-current' : '' ?>">Réglages</a>
            <?php endif; ?>

            <!-- Sys admin only -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "System Administrator"): ?>
                <a href="admin-panel.php" class="<?= ($currentPageName == 'admin-panel.php') ? 'nav-buttons-current' : '' ?>">Panel admin</a>
            <?php endif; ?>
        </nav>
    </div>
    <nav class="nav-right-container">
        <a href="account-management.php" class="<?= ($currentPageName == 'account-management.php') ? 'profile-btn-selected' : 'profile-btn' ?>"><?= $displayName ?></a>
        <a href="../actions/logout_action.php" class="log-out">
            <img src="../assets/log-out.png" alt="Déconnexion"/>
        </a>
    </nav>
</header>
