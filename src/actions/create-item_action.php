<?php
session_start();

include_once("../includes/functions.php");

if (isset($_POST["item-device_type"], $_POST["submit"], $_POST["item-serial_number"])) {
    $device_type = $_POST['item-device_type'] ?? '';

    switch ($device_type) {
        case 'COMPUTER':
            // Map form fields to the keys expected by addComputer function
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

            $result = addComputer($attr);
            
            if ($result['state']) {
                $_SESSION['notification'] = "Ordinateur créé avec succès !";
                $_SESSION['notification_color'] = "#5CE65C";
            } else {
                $_SESSION['notification'] = "Erreur : " . $result['message'];
                $_SESSION['notification_color'] = "red";
            }
            
            break;
        case 'MONITOR':
            $attached_to = $_POST['item-attached_to_computer'] ?? 'null';
            if ($attached_to === 'null') {
                $attached_to = null;
            }

            $attr = [
                'SERIAL' => $_POST['item-serial_number'] ?? '',
                'MANUFACTURER' => $_POST['item-manufacturer_name'] ?? '',
                'MODEL' => $_POST['item-model'] ?? '',
                'SIZE_INCH' => $_POST['item-size'] ?? '',
                'RESOLUTION' => $_POST['item-resolution'] ?? '',
                'CONNECTOR' => $_POST['item-connector_name'] ?? '',
                'ATTACHED_TO' => $attached_to,
                'STATE' => $_POST['item-state'] ?? 'En stock'
            ];

            $result = addMonitor($attr);

            if ($result['state']) {
                $_SESSION['notification'] = "Écran créé avec succès !";
                $_SESSION['notification_color'] = "#5CE65C";
            } else {
                $_SESSION['notification'] = "Erreur : " . $result['message'];
                $_SESSION['notification_color'] = "red";
            }
            break;
        default:
            $_SESSION['notification'] = "Type d'appareil inconnu.";
            $_SESSION['notification_color'] = "red";
            break;
    }
    

    header("Location: ../pages/create-item.php");
    exit();
}
header("Location: ../pages/create-item.php");
exit();
?>