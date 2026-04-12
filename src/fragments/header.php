<?php
    // Get the current page name without the path
    $currentPageName = basename($_SERVER['PHP_SELF']);

    $displayName = htmlspecialchars($_SESSION['login'] ?? 'Profil');
    if (!empty($_SESSION['first_name']) || !empty($_SESSION['last_name'])) {
        $displayName = htmlspecialchars(trim(($_SESSION['first_name'] ?? '') . ' ' . ($_SESSION['last_name'] ?? '')));
    }
?>
<header>
    <div class="nav-left-container">
        <div class="app-name-container">
            <a href="index.php">KEEPIT</a>
        </div>
        <nav class="nav-buttons-container">
            <a href="index.php" class="<?= ($currentPageName == 'index.php') ? 'nav-buttons-current' : '' ?>">Dashboard</a>
            <a href="inventory.php" class="<?= (in_array($currentPageName, ['inventory.php', 'inventory-item.php', 'create-item.php', 'importCSV.php'])) ? 'nav-buttons-current' : '' ?>">Inventaire</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'Web Administrator'): ?>
                <a href="technician.php" class="<?= (in_array($currentPageName, ['technician.php', 'create-technician.php'])) ? 'nav-buttons-current' : '' ?>">Techniciens</a>
            <?php endif; ?>
            <a href="#">Informations</a>
        </nav>
    </div>
    <nav class="nav-right-container">
        <a href="account-management.php" class="<?= ($currentPageName == 'account-management.php') ? 'profile-btn-selected' : 'profile-btn' ?>"><?= $displayName ?></a>
        <a href="../actions/logout_action.php" class="log-out">
            <img src="../assets/log-out.png" alt="Déconnexion"/>
        </a>
    </nav>
</header>
