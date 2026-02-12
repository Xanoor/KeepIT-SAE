<?php
session_start();
if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
    header("Location: ../pages/login.php");
    exit();
}

include_once '../includes/functions.php';

if (isset($_POST["item-device_type"], $_POST["submit"], $_POST["item-serial_number"])) {
    $device_type = $_POST['item-device_type'] ?? '';
    $serial_number = $_POST["item-serial_number"] ?? '';

    switch ($device_type) {
        case 'COMPUTER':
            // Map form fields to the keys expected
            $attr = [
                'NAME' => $_POST['item-name'] ?? '',
                'SERIAL' => $_POST['item-serial_number'] ?? '',
                'MANUFACTURER' => $_POST['item-manufacturer_name'] ?? '',
                'MODEL' => $_POST['item-model'] ?? '', 
                'TYPE' => $_POST['item-type_name'] ?? '',
                'CPU' => $_POST['item-cpu'] ?? '',
                'RAM_MB' => $_POST['item-ram_mb'] ?? '',
                'DISK_GB' => $_POST['item-disk_gb'] ?? '',
                'OS' => $_POST['item-os_name'] ?? '',
                'DOMAIN' => $_POST['item-domain'] ?? '',
                'LOCATION' => $_POST['item-location'] ?? '',
                'BUILDING' => $_POST['item-building'] ?? '',
                'ROOM' => $_POST['item-room'] ?? '',
                'MACADDR' => $_POST['item-mac_address'] ?? '',
                'PURCHASE_DATE' => $_POST['item-purchase_date'] ?? '',
                'WARRANTY_END' => $_POST['item-warranty_end'] ?? '',
                'STATE' => $_POST['item-state'] ?? 'En stock'
            ];

            if (!checkDatabaseExistence($connect, 'computer', 'serial_number', $attr["SERIAL"])) {
                $_SESSION['notification'] = "L'ordinateur avec le numéro de série ".$attr["SERIAL"]." n'est pas dans la base de données";
                $_SESSION['notification_color'] = "red";
                header("Location: ../pages/inventory-item.php?serialNumber=".$attr["SERIAL"]."&deviceType=".$device_type);
                exit();
            }
            if (!checkDatabaseExistence($connect, 'devices', 'serial_number', $attr["SERIAL"])) {
                $_SESSION['notification'] = "Un appareil avec le numéro de série ".$attr["SERIAL"]." n'est pas dans la base de données";
                $_SESSION['notification_color'] = "red";
                header("Location: ../pages/inventory-item.php?serialNumber=".$attr["SERIAL"]."&deviceType=".$device_type);
                exit();
            }

            $checkComputerAttributes = checkComputerAttributes($attr);
            if (!$checkComputerAttributes["state"]) {
                $_SESSION['notification'] = "Modification échouées: ".$checkComputerAttributes["message"];
                $_SESSION['notification_color'] = "red";
                header("Location: ../pages/inventory-item.php?serialNumber=".$attr["SERIAL"]."&deviceType=".$device_type);
                exit();
            }
            $attr = $checkComputerAttributes["attr"];

            mysqli_begin_transaction($connect);
            try {
                // Update DEVICES first (Parent)
                $req_devices = "UPDATE devices SET model = ?, state = ? WHERE serial_number = ? AND device_type = 'Computer'";
                $stmt_dev = mysqli_prepare($connect, $req_devices);
                mysqli_stmt_bind_param($stmt_dev, "sss", 
                    $attr["MODEL"], $attr["STATE"], $attr["SERIAL"]
                );
                
                if (!mysqli_stmt_execute($stmt_dev)) {
                    throw new Exception("Erreur lors de la modification dans la table devices : " . mysqli_stmt_error($stmt_dev));
                }

                // Update COMPUTER next (Child)
                $req_comp = "UPDATE computer SET name = ?, location = ?, building = ?, room = ?, cpu = ?, ram_mb = ?, disk_gb = ?, domain = ?, mac_address = ?, purchase_date = ?, warranty_end = ?, manufacturer_name = ?, os_name = ?, type_name = ?
                WHERE serial_number = ?";
                $stmt_comp = mysqli_prepare($connect, $req_comp);
                mysqli_stmt_bind_param($stmt_comp, "sssssssssssssss", 
                    $attr["NAME"], $attr["LOCATION"], $attr["BUILDING"], $attr["ROOM"], $attr["CPU"], $attr["RAM_MB"], $attr["DISK_GB"], 
                    $attr["DOMAIN"], $attr["MACADDR"], $attr["PURCHASE_DATE"], $attr["WARRANTY_END"], 
                    $attr["MANUFACTURER"], $attr["OS"], $attr["TYPE"], $attr["SERIAL"]
                );
                if (!mysqli_stmt_execute($stmt_comp)) {
                    throw new Exception("Erreur lors de la modification dans la table computer : " . mysqli_stmt_error($stmt_comp));
                }
                // Everything worked
                mysqli_commit($connect);
                $_SESSION['notification'] = "Modifications effectuées.";
                $_SESSION['notification_color'] = "#5CE65C";
            } catch (Exception $e) {
                // Something failed -> Undo everything
                mysqli_rollback($connect);
                $_SESSION['notification'] = "Modification echouées: ".$e->getMessage();
                $_SESSION['notification_color'] = "red";
                header("Location: ../pages/inventory-item.php?serialNumber=".$attr["SERIAL"]."&deviceType=".$device_type);
                exit();
            }
            header("Location: ../pages/inventory-item.php?serialNumber=".$attr["SERIAL"]."&deviceType=".$device_type);
            exit();


        case 'MONITOR':
            // TODO: create monitor version
            break;


        default:
            $_SESSION['notification'] = "Type d'appareil inconnu.";
            $_SESSION['notification_color'] = "red";
            header("Location: ../pages/inventory.php");
            exit();
    }
} else if (isset($_POST["item-device_type"], $_POST["submit-delete"], $_POST["item-serial_number"])) {
    // TODO: create delete code
} else {
    header("Location: ../pages/inventory.php");
    exit();
}

?>