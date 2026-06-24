<?php
    // Maintenance has its own file because it doesn't need MySQL to work

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    function getMaintenanceFile() {
        return __DIR__ . '/../maintenance.flag';
    }

    function isMaintenanceActive() {
        return file_exists(getMaintenanceFile());
    }

    function canBypassMaintenance() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'System Administrator';
    }

    function checkMaintenance() {
        if (isMaintenanceActive() && !canBypassMaintenance()) {
            header("Location: maintenance.php");
            exit;
        }
    }

?>

