<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function create_connection() {
    if (!isset($GLOBALS['connect'])) {
        // Disables MySQL exceptions/fatal errors on connection failure so we can handle it manually
        mysqli_report(MYSQLI_REPORT_OFF);
        $GLOBALS['connect'] = @mysqli_connect("localhost", "root", "", "keepit");

        // Check connection
        require_once __DIR__ . '/maintenance-fnc.php';
        if (!$GLOBALS['connect']) {
            if (!isMaintenanceActive()) {
                file_put_contents(getMaintenanceFile(), "Maintenance auto activée (BDD hors ligne)");
            }
            checkMaintenance();
            // If the user is a sysadmin (bypassing maintenance), we still stop to prevent MySQL errors
            die("Erreur critique : Service MySQL hors ligne.");
        } else {
            if (isMaintenanceActive()) {
                $maintenanceContent = file_get_contents(getMaintenanceFile());
                if (trim($maintenanceContent) === "Maintenance auto activée (BDD hors ligne)") {
                    unlink(getMaintenanceFile());
                }
            }
        }

        mysqli_set_charset($GLOBALS['connect'], "utf8");
    }
}

create_connection();

// If the database connection is recreated, we need to re-identify the user logged-in.
// So we set the user login again in the @current_user MySQL session variable.
// variables starting with @ in MySQL are session variables

if (isset($_SESSION['login'])) {
    $login_safe = mysqli_real_escape_string(
        $GLOBALS['connect'],
        $_SESSION['login']
    );

    mysqli_query(
        $GLOBALS['connect'],
        "SET @current_user = '$login_safe'"
    );
}

require_once __DIR__ . '/ban-fnc.php';
checkIpBan();