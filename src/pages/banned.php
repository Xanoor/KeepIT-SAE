<?php
    require_once '../includes/db.php';

    $ip = $_SERVER['REMOTE_ADDR'];
    $is_banned = false;
    $reason = null;

    if (isset($GLOBALS['connect'])) {
        $query = "SELECT reason FROM ip_ban WHERE ip_address = INET6_ATON(?)";
        $stmt = mysqli_prepare($GLOBALS['connect'], $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $ip);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $db_reason);
            if (mysqli_stmt_fetch($stmt)) {
                $is_banned = true;
                $reason = $db_reason;
                $_SESSION['ban_reason'] = $reason;
            }
            mysqli_stmt_close($stmt);
        }
    }

    if (!$is_banned) {
        unset($_SESSION['ban_reason']);
        header("Location: login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Accès Interdit</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="../styles/global.css" />
    <link rel="stylesheet" type="text/css" href="../styles/banned.css" />
</head>
<body>
    <main>
        <div id="banned-header">
            <img alt="Logo du site" src="../assets/logo.png" />
            <h1>KEEPIT</h1>
        </div>

        <h2>Accès Refusé</h2>

        <p class="banned-message">
            Votre adresse IP a été bannie de notre service suite à plusieurs tentatives de connexion infructueuses.
        </p>

        <?php if (!empty($reason)): ?>
            <div class="banned-reason">
                <strong>Raison du bannissement :</strong>
                <p><?php echo htmlspecialchars($reason); ?></p>
            </div>
        <?php endif; ?>

        <p class="banned-message-sub">
            Veuillez contacter un administrateur système si vous estimez qu'il s'agit d'une erreur.
        </p>
        
        <div class="banned-actions">
            <a href="../actions/logout_action.php" class="logout-btn">Retourner à la connexion</a>
        </div>
    </main>
</body>
</html>
