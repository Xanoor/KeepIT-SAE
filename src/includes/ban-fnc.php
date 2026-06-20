<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    /**
     * Checks if the client IP is banned and redirects them to the banned page if so.
     */
    function checkIpBan() {
        if (isset($GLOBALS['connect'])) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            if ($currentPage !== 'banned.php') {
                $ip = $_SERVER['REMOTE_ADDR'];
                $query = "SELECT reason FROM ip_ban WHERE ip_address = INET6_ATON(?)";
                $stmt = mysqli_prepare($GLOBALS['connect'], $query);
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $ip);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $reason);
                    $is_banned = mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);

                    if ($is_banned) {
                        $_SESSION['ban_reason'] = $reason;
                        header("Location: banned.php");
                        exit();
                    }
                }
            }
        }
    }
?>
