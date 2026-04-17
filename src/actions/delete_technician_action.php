<?php
session_start();

// Only the web admin can access this action
if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] !== 'Web Administrator') {
    header("Location: ../pages/login.php");
    exit();
}

include_once("../includes/functions.php");

if (isset($_POST['login_tech'])) {

    if ($_POST['login_tech'] === 'tech1'){
        $_SESSION['notification'] = "Vous ne pouvez pas supprimer cet utilisateur.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/technician.php");
        exit();
    }
    $query = "DELETE FROM users WHERE login = ? AND role = 'Technician'";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $_POST['login_tech']);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['notification'] = "Technicien supprimé avec succès.";
        $_SESSION['notification_color'] = "green";
    } else {
        $_SESSION['notification'] = "Erreur lors de la suppression.";
        $_SESSION['notification_color'] = "red";
    }

}

header("Location: ../pages/technician.php");
mysqli_stmt_close($stmt);
exit();