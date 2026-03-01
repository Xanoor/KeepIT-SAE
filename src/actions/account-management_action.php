<?php
    session_start();
    require_once '../includes/db.php';

    if (isset($_POST['submit'])) {
        
        $db = $GLOBALS['connect'];
        $login = $_SESSION['login'];

        // We need to check if the login is a "static login' like tech1, adminsys... Normally we can't access to account-managament.php with these logins. 
        // But in the case we can, we need to check if the login is one of these
        if ($login === "tech1" || $login === "sysadmin" || $login === "adminweb") {
            $_SESSION['notification'] = "Vous ne pouvez pas modifier les informations de ce compte.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/account-management.php");
            exit();
        }

        // now we can update the account.        
        $new_firstname = $_POST['first-name'];
        $new_lastname = $_POST['last-name'];
        $new_password = $_POST['password'];

        $sql = "SELECT first_name, last_name, password_hash FROM users WHERE login = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "s", $login);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $resultat = mysqli_fetch_assoc($result);

            // Checing if the new values are the same as the old ones, if so we don't update and we notify the user that no modification has been made to his account.
            if($resultat['first_name'] === $new_firstname && $resultat['last_name'] === $new_lastname && password_verify($new_password, $resultat['password_hash'])) {
                $_SESSION['notification'] = "Aucune modification n'a été apportée à votre compte.";
                $_SESSION['notification_color'] = "red";
                header("Location: ../pages/account-management.php");
                exit();
            }

            // Checking if the password is the same one, in the other case, we change and notify the user that he has been changed.
            if (!password_verify($new_password, $resultat['password_hash']) && !empty($new_password)) {
                $password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET password_hash = ? WHERE login = ?";
                $stmt = mysqli_prepare($db, $sql);
                mysqli_stmt_bind_param($stmt, "ss", $password, $login);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['notification'] = "Mot de passe mis à jour avec succès.";
                    $_SESSION['notification_color'] = "green";
                    header("Location: ../pages/account-management.php");
                    exit();

                } else {
                    $_SESSION['notification'] = "Erreur lors de la mise à jour du mot de passe.";
                    $_SESSION['notification_color'] = "red";
                    header("Location: ../pages/account-management.php");
                    exit();
                }
            }

            // Now we check if the first or the last name has been changed, in the case, we notify the user.
            if ($resultat['first_name'] !== $new_firstname || $resultat['last_name'] !== $new_lastname) {
                $sql = "UPDATE users SET first_name = ?, last_name = ? WHERE login = ?";
                $stmt = mysqli_prepare($db, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $new_firstname, $new_lastname, $login);

                if (mysqli_stmt_execute($stmt)) {
                    $_SESSION['notification'] = "Informations personnelles mises à jour avec succès.";
                    $_SESSION['notification_color'] = "green";
                    header("Location: ../pages/account-management.php");
                    exit();
                } else {
                    $_SESSION['notification'] = "Erreur lors de la mise à jour des informations personnelles.";
                    $_SESSION['notification_color'] = "red";
                    header("Location: ../pages/account-management.php");
                    exit();
                }
            }

        
        } 

    mysqli_stmt_close($stmt);
    header("Location: ../pages/account-management.php");
    exit();
    }
