<?php

session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

echo "Infos de l'utilisateur connecté :<br>";
print_r($_SESSION);
echo "<br><br>";

echo "Dernière connexion : " . date("d/m/Y H:i:s", $_SESSION['last_activity']);
echo "<br><br>";
echo "<a href='../actions/logout_action.php'>Se déconnecter</a>";