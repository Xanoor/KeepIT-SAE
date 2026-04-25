<?php
    // TODO: create admin settings new variable actions
    session_start();

    // Only the web admin can access this action
    if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] !== 'Web Administrator') {
        header("Location: ../pages/login.php");
        exit();
    }

    include_once("../includes/functions.php");

    // Delete var button
    if (isset($_POST["DELETE_VAR"], $_POST["var-items"], $_POST["var-column"], $_POST["var-name"])) {
        $tableName = $_POST["var-name"];
        $columnName = $_POST["var-column"];
        $varValue = $_POST["var-items"];

        // Verify that the table, column, and value exist
        if (!tableExists($connect, $tableName) || !columnsExists($connect, $tableName, [$columnName]) || !checkDatabaseExistence($connect, $tableName, $columnName, $varValue)) {
            $_SESSION['notification'] = "Erreur : La variable sélectionnée est invalide ou introuvable.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/admin-settings.php");
            exit();
        }

        // Check if the value is still referenced in other tables
        $refs = getForeignKeysReferencing($connect, $tableName, $columnName);
        $isUsed = false;
        
        foreach ($refs as $foreignKey) {
            if (checkDatabaseExistence($connect, $foreignKey["TABLE_NAME"], $foreignKey["COLUMN_NAME"], $varValue)) {
                $isUsed = true;
                break;
            }
        }

        if ($isUsed) {
            $_SESSION['notification'] = "Cette valeur est toujours utilisée dans le site.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/admin-settings.php");
            exit();
        }

        // Delete the variable
        try {
            $delete_var = "DELETE FROM `$tableName` WHERE `$columnName` = ?";
            $stmt_del_var = mysqli_prepare($connect, $delete_var);
            mysqli_stmt_bind_param($stmt_del_var, "s", $varValue);
            
            if (!mysqli_stmt_execute($stmt_del_var)) {
                throw new Exception("Erreur SQL : " . mysqli_stmt_error($stmt_del_var));
            }

            $_SESSION['notification'] = "Variable supprimée avec succès.";
            $_SESSION['notification_color'] = "#5CE65C";
        } catch (Exception $e) {
            $_SESSION['notification'] = "Erreur lors de la suppression : " . $e->getMessage();
            $_SESSION['notification_color'] = "red";
        }

        header("Location: ../pages/admin-settings.php");
        exit();
    }
?>