<?php
    session_start();

    // Only the web admin can access this action
    if (!isset($_SESSION['login'], $_SESSION['role']) || $_SESSION['role'] !== 'Web Administrator') {
        header("Location: ../pages/login.php");
        exit();
    }

    include_once("../includes/functions.php");
    global $connect; 

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
    } else if (isset($_POST["table-select"], $_POST["var-value"], $_POST["CREATE_VAR"])) {
        $tableName = $_POST["table-select"];
        $varValue = $_POST["var-value"];
        
        if (!tableExists($connect, $tableName)) {
            $_SESSION['notification'] = "La table ".$tableName." n'est pas présente dans la base de donnée.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/admin-settings.php");
            exit();
        }

        $columnName = "";
           
        // Depending on the table selected, execute a customized SELECT query. 
        // For example: if a table requires multiple values, then create a new case.
        switch ($tableName) {
            case 'connector':
            case 'operating_system':
            case 'manufacturer':
                $columnName = "name";
                break;

            case 'locations':
                $columnName = "location";
                break;

            default:
                $_SESSION['notification'] = "L'action liée a cette table n'a pas été spécifiée dans le code.";
                $_SESSION['notification_color'] = "red";
                header("Location: ../pages/admin-settings.php");
                exit();
        }
        // if the query is the default one (only one column to edit), use the query:

        if (!columnsExists($connect, $tableName, [$columnName])) {
            $_SESSION['notification'] = "La colonne pour la table ".$tableName." est introuvable.".$columnName;
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/admin-settings.php");
            exit();
        }

        if (checkDatabaseExistence($connect, $tableName, $columnName, $varValue)) {
            $_SESSION['notification'] = "La valeur ".$varValue." est déja présente dans la table.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/admin-settings.php");
            exit();
        }

        $add_query = "INSERT INTO $tableName ($columnName) VALUES (?)";
        $add_stmt = mysqli_prepare($connect, $add_query);
        mysqli_stmt_bind_param($add_stmt, "s", 
            $varValue
        );
        
        if (!mysqli_stmt_execute($add_stmt)) {
            $_SESSION['notification'] = "Une erreur est arrivée pendant l'insertion de la valeur.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/admin-settings.php");
            exit();
        }

        $_SESSION['notification'] = "La valeur ".$varValue." à été ajouté a la table ".$tableName;
        $_SESSION['notification_color'] = "#5CE65C";
        header("Location: ../pages/admin-settings.php");
        exit();
    }
?>