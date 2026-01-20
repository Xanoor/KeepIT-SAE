<?php

session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

echo "Infos de l'utilisateur connecté :<br>";
print_r($_SESSION);

echo "Dernière connexion : " . date("d/m/Y H:i:s", $_SESSION['last_activity']);