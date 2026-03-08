<?php
    session_start();
    if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
        header("Location: ../pages/login.php");
        exit();
    }

    include_once("../includes/functions.php");

    if(!empty($_POST["login"]) && !empty($_POST["password"])){
        $new_password = $_POST["password"];
        $login = $_POST["login"];
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // We check if the login/password changed
        $users = getPasswordFromLogin($_POST["login"]);
        $user = mysqli_fetch_assoc($users);
        $old_password = $user['password_hash'] ?? '';
        

        if(password_verify($new_password, $old_password)){ // The password remain the same
            $_SESSION['notification'] = "Le nouveau mot de passe est identique à l'ancien.";
            $_SESSION['notification_color'] = "orange";
            header("Location: ../pages/technician.php");
            exit();
        }
        else{
            $query = "UPDATE users SET password_hash = ? WHERE login = ?";
            $stmt = mysqli_prepare($GLOBALS['connect'], $query);
            mysqli_stmt_bind_param($stmt, "ss", $hashed_password, $login);
            if(mysqli_stmt_execute($stmt)){
                $_SESSION['notification'] = "Mot de passe mis à jour avec succès !";
                $_SESSION['notification_color'] = "green";
            }
            else{
                $_SESSION['notification'] = "Erreur sql lors de la mise à jour.";
                $_SESSION['notification_color'] = "red";
            }
        }
    }
    else{
        $_SESSION['notification'] = "Veuillez remplir tous les champs.";
        $_SESSION['notification_color'] = "red";
    }        
    header("Location: ../pages/technician.php");
    exit();
    
?>
