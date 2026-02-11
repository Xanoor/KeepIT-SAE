<?php

session_start();

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

echo "Infos de l'utilisateur connecté :<br>";
print_r($_SESSION);
echo "<br><br>";
echo "Lien vers l'inventaire: <a href='../pages/inventory.php'>Inventaire</a>";
echo "<br>";
echo "Dernière connexion : " . date("d/m/Y H:i:s", strtotime("+1 hour"));
echo "<br><br>";
echo "<a href='../actions/logout_action.php'>Se déconnecter</a>";