<?php
require_once '../fragments/db.php';

include '../fragments/functions.php';
include '../components/icon.php';

$error_code = $_GET['error'] ?? null;

session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Se connecter</title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="../styles/global.css" />
    <link rel="stylesheet" type="text/css" href="../styles/login.css" />
</head>
<body>
    <main>
        <div id="login-header">
            <img alt="Logo du site" src="../assets/logo.png" />
            <h1>KEEPIT</h1>
        </div>
        <form method="POST" action="../actions/login_action.php">

            <h2>Connexion</h2>

            <?php if ($error_code == 1) { ?>
                <p class="error-message">Veuillez remplir tous les champs</p>
            <?php } elseif ($error_code == 2) { ?>
                <p class="error-message">Identifiants incorrects</p>
            <?php } ?>

            <div>
                <label for="login">Login</label>
                <input
                    id="login"
                    name="login"
                    type="text"
                    placeholder="Login"
                />

                <label for="password">Mot de passe</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="******"
                />
            </div>
            <input name="submit" type="submit" value="Se connecter" />
        </form>

        <input
            type="button"
            id="video-link"
            value="Vidéo de présentation"
        />
    </main>
</body>
</html>