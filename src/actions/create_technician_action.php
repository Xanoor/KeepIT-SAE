<?php
session_start();
include_once("../includes/functions.php");

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'Web Administrator') {
    header("Location: ../pages/technician.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $login = trim($_POST['login']);
    $password = $_POST['password'];

    if (empty($first_name) || empty($last_name) || empty($login) || empty($password)) {
        $_SESSION['notification'] = "Tous les champs sont obligatoires.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/create-technician.php");
        exit();
    }

    // We check if the login exist in the database
    $check_query = "SELECT login FROM users WHERE login = ?";
    $stmt_check = mysqli_prepare($connect, $check_query);
    mysqli_stmt_bind_param($stmt_check, "s", $login);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $_SESSION['notification'] = "Ce login est déjà utilisé.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/create-technician.php");
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $role = 'Technician';

    $insert_query = "INSERT INTO users (login, password_hash, first_name, last_name, role, created_at) 
                     VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt_insert = mysqli_prepare($connect, $insert_query);
    mysqli_stmt_bind_param($stmt_insert, "sssss", $login, $hashed_password, $first_name, $last_name, $role);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['notification'] = "Le compte technicien a été créé avec succès.";
        $_SESSION['notification_color'] = "green"; // Vert
        header("Location: ../pages/technician.php");
    } else {
        $_SESSION['notification'] = "Erreur lors de la création du compte.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/create-technician.php");
    }

    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_insert);
    exit();
}