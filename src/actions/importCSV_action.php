<?php
session_start();
if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
    header("Location: ../pages/login.php");
    exit();
}

require_once '../includes/functions.php';

$header_computer = array("NAME","SERIAL","MANUFACTURER","MODEL","TYPE","CPU","RAM_MB","DISK_GB","OS","DOMAIN","LOCATION","BUILDING","ROOM","MACADDR","PURCHASE_DATE","WARRANTY_END");
$header_screen = array("SERIAL","MANUFACTURER","MODEL","SIZE_INCH","RESOLUTION","CONNECTOR","ATTACHED_TO");

if (isset($_POST["device_type"], $_POST["file_path_csv"], $_POST["import_csv"])) {
    $filePath = $_POST["file_path_csv"];
    $device_type = $_POST["device_type"];

    $uploadDir = realpath("../uploads/");
    $realPath = realpath($filePath);

    // Verify if file path is valid
    if (file_exists($filePath) && str_starts_with($realPath, $uploadDir)) { 
        // Remove tmp file at the exit()
        register_shutdown_function(function() use ($realPath) {
            if (file_exists($realPath)) {
                unlink($realPath);
            }
        });

        $csv_file = fopen($filePath, "r");
        $header_csv = fgetcsv($csv_file);

        $header_count = 0;
        $header_valid_count = NULL;
        $device_header = NULL;

        switch ($device_type) {
            case 'CENTRAL_UNIT':
                $header_valid_count = count($header_computer);
                $device_header = $header_computer;
                break;
            case 'SCREEN':
                $header_valid_count = count($header_screen);
                $device_header = $header_screen;
                break;
            default:
                $_SESSION['notification'] = "Type d'appareil invalide !";
                header("location: ../pages/importCSV.php");
                exit();
                break;
        }

        // Check if header is valid
        foreach ($header_csv as $value) {
            $header_count++;
            if (!in_array($value, $device_header)) {
                $_SESSION['notification'] = "L'attribut ".$value." n'est pas valide.";
                header(("location: ../pages/importCSV.php"));
                exit();
            }
        }

        if ($header_count != $header_valid_count) {
            $_SESSION['notification'] = "Nombre d'attributs invalide. (".$header_count."/".$header_valid_count.")";
            header(("location: ../pages/importCSV.php"));
            exit();
        }

        $errors = array();
        $totalImported = 0;

        switch ($device_type) {
            case 'CENTRAL_UNIT':
                $line = 1;
                while ($row = fgetcsv($csv_file)) {
                    $line++;
                    if(count($row) == count($header_csv)){
                       $result = addComputer(array_combine($header_csv, $row), $line);
                       if (!$result["state"]) {
                           $errors[] = $result["message"];
                       } else $totalImported++;
                    }
                }
                break;
            case 'SCREEN':
                $line = 1;
                while ($row = fgetcsv($csv_file)) {
                    $line++;
                    if(count($row) == count($header_csv)){
                       $result = addMonitor(array_combine($header_csv, $row), $line);
                       if (!$result["state"]) {
                           $errors[] = $result["message"];
                       } else $totalImported++;
                    }
                }
                break;
            default:
                # code...
                break;
        }

        // If one or more element is not imported
        if (!empty($errors)) {
            $_SESSION['import_errors'] = $errors;
            $_SESSION['notification'] = "Des erreurs sont survenues lors de l'importation. $totalImported éléments importés.";
            $_SESSION['notification_color'] = "red";
            header("location: ../pages/importCSV.php");
            exit();
        }

        $_SESSION['notification'] = "Données importées.";
        $_SESSION['notification_color'] = "#5CE65C";

        header("location: ../pages/inventory.php");
        exit();
    } else {
        $_SESSION['notification'] = "Fichier introuvable !";
        header("location: ../pages/importCSV.php");
        exit();
    }
} else {
    header("location: ../pages/importCSV.php");
    exit();
}

?>