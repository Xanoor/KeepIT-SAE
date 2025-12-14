<?php 

require_once 'db.php';

/**
 * Create an HTML table from a CSV file.
 *
 * @param resource $file File pointer to the CSV file.
 * @param int $limit Maximum number of rows to read (default 100).
 * @return string HTML table.
 */
function importTableBuilder($file, $limit = 100) {
    $result = fgetcsv($file);

    // Header
    $html = "<table><thead><tr>";
    foreach ($result as $value) {
        $html .= "<th>" . htmlspecialchars($value) . "</th>";
    }
    $html .= "</tr></thead><tbody>";

    $counter = 0;
    // Body
    while (($result = fgetcsv($file)) && $counter < $limit) {
        $counter++;
        $html .= "<tr>";
        foreach ($result as $value) {
            $html .= "<td>" . htmlspecialchars($value) . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</tbody></table>";

    return $html;
}

function addMonitor($attr, $line = "?") {

}

function addComputer($attr, $line = "?") {    
    global $connect;
    // Verify if all attributs are in the dict
    $required_keys = [
        'NAME', 'SERIAL', 'MANUFACTURER', 'MODEL', 'TYPE', 'CPU', 
        'RAM_MB', 'DISK_GB', 'OS', 'DOMAIN', 'LOCATION', 'BUILDING', 
        'ROOM', 'MACADDR', 'PURCHASE_DATE', 'WARRANTY_END'
    ];
    $required_keys_model = array_flip($required_keys);
    $missing_keys = array_diff_key($required_keys_model, $attr);
    if (!empty($missing_keys)) { // A key is missing !
        return ["state" => false, "message" => "Line $line: Missing keys."];
    }

    $attr["PURCHASE_DATE"] = date('Y-m-d', strtotime(str_replace('/', '-', $attr["PURCHASE_DATE"])));
    $attr["WARRANTY_END"]  = date('Y-m-d', strtotime(str_replace('/', '-', $attr["WARRANTY_END"])));

    // Verify if already in DB
    $stmt_check_serial = mysqli_prepare($connect, "SELECT serial_number FROM computer WHERE serial_number = ?");
    mysqli_stmt_bind_param($stmt_check_serial, "s", $attr["SERIAL"]);
    mysqli_stmt_execute($stmt_check_serial);
    $res_check_serial = mysqli_stmt_get_result($stmt_check_serial);
    if (mysqli_num_rows($res_check_serial) > 0) {
        return ["state" => false, "message" => "Line $line: The computer with serial ".$attr["SERIAL"]." is already in the database"];
    }

    //  --------------- Verify Manufacturer ---------------
    $stmt_check_manu = mysqli_prepare($connect, "SELECT name FROM manufacturer WHERE name = ?");
    mysqli_stmt_bind_param($stmt_check_manu, "s", $attr["MANUFACTURER"]);
    mysqli_stmt_execute($stmt_check_manu);
    mysqli_stmt_store_result($stmt_check_manu);
    if (mysqli_stmt_num_rows($stmt_check_manu) == 0) {
        return ["state" => false, "message" => "Line $line: The Manufacturer ".$attr["MANUFACTURER"]." isn't registered"];
    }

    //   --------------- Verify OS ---------------
    $stmt_check_os = mysqli_prepare($connect, "SELECT name FROM operating_system WHERE name = ?");
    mysqli_stmt_bind_param($stmt_check_os, "s", $attr["OS"]);
    mysqli_stmt_execute($stmt_check_os);
    mysqli_stmt_store_result($stmt_check_os);
    if (mysqli_stmt_num_rows($stmt_check_os) == 0) {
        return ["state" => false, "message" => "Line $line: The OS ".$attr["OS"]." isn't registered"];
    }

    //  --------------- Verify Type ---------------
    $stmt_check_type = mysqli_prepare($connect, "SELECT name FROM computer_type WHERE name = ?");
    mysqli_stmt_bind_param($stmt_check_type, "s", $attr["TYPE"]);
    mysqli_stmt_execute($stmt_check_type);
    mysqli_stmt_store_result($stmt_check_type);
    if (mysqli_stmt_num_rows($stmt_check_type) == 0) {
        return ["state" => false, "message" => "Line $line: The computer type ".$attr["TYPE"]." isn't registered"];
    }

    // SQL INSERT REQUEST
    $request_add_computer = "INSERT INTO computer (serial_number, name, model, cpu, ram_mb, disk_gb, domain, location, building, room, mac_address, purchase_date, warranty_end, manufacturer_name, os_name, type_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_add_computer = mysqli_prepare($connect, $request_add_computer);
    mysqli_stmt_bind_param($stmt_add_computer, "ssssssssssssssss", $attr["SERIAL"], $attr["NAME"], $attr["MODEL"], $attr["CPU"], $attr["RAM_MB"], $attr["DISK_GB"], $attr["DOMAIN"], $attr["LOCATION"], $attr["BUILDING"], $attr["ROOM"], $attr["MACADDR"], $attr["PURCHASE_DATE"], $attr["WARRANTY_END"], $attr["MANUFACTURER"], $attr["OS"], $attr["TYPE"]);
    
    try {
        if (mysqli_stmt_execute($stmt_add_computer)) {
            return ["state" => true];
        } else {
            return ["state" => false, "message" => "Line $line: Database error: " . mysqli_stmt_error($stmt_add_computer)];
        }
    } catch (Exception $e) {
        return ["state" => false, "message" => "Line $line: Database error."];
    }
}

?>