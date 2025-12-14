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

/**
 * Check if specific keys are present in the attributes array.
 */
function verifyRequiredKeys($attr, $required_keys, $line) {
    $required_keys_model = array_flip($required_keys);
    $missing_keys = array_diff_key($required_keys_model, $attr);
    if (!empty($missing_keys)) { 
        return ["state" => false, "message" => "Line $line: Missing keys."];
    }
    return ["state" => true];
}

/**
 * Check if a value exists in a specific table and column.
 */
function checkDatabaseExistence($connect, $table, $column, $value) {
    if (empty($value)) return false; // Handle null/empty

    $request = "SELECT $column FROM $table WHERE $column = ?";
    $stmt = mysqli_prepare($connect, $request);
    mysqli_stmt_bind_param($stmt, "s", $value);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $rows = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
    return $rows > 0;
}

/**
 * Add a monitor to the database.
 * 
 * @param array $attr Associative array containing monitor attributes.
 * @param int|string $line Line number in the CSV file (for error reporting).
 * @return array "state" (bool) and "message" (string) on error.
 */
function addMonitor($attr, $line = "?") {
    global $connect;
    $required_keys = ["SERIAL","MANUFACTURER","MODEL","SIZE_INCH","RESOLUTION","CONNECTOR","ATTACHED_TO"];
    
    $checkKeys = verifyRequiredKeys($attr, $required_keys, $line);
    if (!$checkKeys["state"]) return $checkKeys;

    // Verify if already in DB
    if (checkDatabaseExistence($connect, 'monitor', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => "Line $line: The monitor with serial ".$attr["SERIAL"]." is already in the database"];
    }

    // Verify Manufacturer
    if (!checkDatabaseExistence($connect, 'manufacturer', 'name', $attr["MANUFACTURER"])) {
        return ["state" => false, "message" => "Line $line: The Manufacturer ".$attr["MANUFACTURER"]." isn't registered"];
    }

    // Verify Connector
    if (!checkDatabaseExistence($connect, 'connector', 'name', $attr["CONNECTOR"])) {
        return ["state" => false, "message" => "Line $line: The Connector ".$attr["CONNECTOR"]." isn't registered"];
    }

     // Verify attached to computer
     if (!empty($attr["ATTACHED_TO"])) {
        if (!checkDatabaseExistence($connect, 'computer', 'serial_number', $attr["ATTACHED_TO"])) {
            return ["state" => false, "message" => "Line $line: The computer ".$attr["ATTACHED_TO"]." isn't registered"];
        }
     } else {
         $attr["ATTACHED_TO"] = null; // set to null
     }


    $request_add_monitor = "INSERT INTO monitor (serial_number, model, size_inch, resolution, manufacturer_name, connector_name, attached_to_serial) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_add_monitor = mysqli_prepare($connect, $request_add_monitor);

    mysqli_stmt_bind_param($stmt_add_monitor, "sssssss", 
        $attr["SERIAL"], 
        $attr["MODEL"], 
        $attr["SIZE_INCH"], 
        $attr["RESOLUTION"], 
        $attr["MANUFACTURER"], 
        $attr["CONNECTOR"], 
        $attr["ATTACHED_TO"]
    );

    try {
        if (mysqli_stmt_execute($stmt_add_monitor)) {
            return ["state" => true];
        } else {
            return ["state" => false, "message" => "Line $line: Database error: " . mysqli_stmt_error($stmt_add_monitor)];
        }
    } catch (Exception $e) {
        return ["state" => false, "message" => "Line $line: Database error."];
    }
}

/**
 * Add a computer to the database.
 * 
 * @param array $attr Associative array containing computer attributes.
 * @param int|string $line Line number in the CSV file (for error reporting).
 * @return array "state" (bool) and "message" (string) on error.
 */
function addComputer($attr, $line = "?") {    
    global $connect;
    // Verify if all attributs are in the dict
    $required_keys = [
        'NAME', 'SERIAL', 'MANUFACTURER', 'MODEL', 'TYPE', 'CPU', 
        'RAM_MB', 'DISK_GB', 'OS', 'DOMAIN', 'LOCATION', 'BUILDING', 
        'ROOM', 'MACADDR', 'PURCHASE_DATE', 'WARRANTY_END'
    ];
    
    $checkKeys = verifyRequiredKeys($attr, $required_keys, $line);
    if (!$checkKeys["state"]) return $checkKeys;

    $attr["PURCHASE_DATE"] = date('Y-m-d', strtotime(str_replace('/', '-', $attr["PURCHASE_DATE"])));
    $attr["WARRANTY_END"]  = date('Y-m-d', strtotime(str_replace('/', '-', $attr["WARRANTY_END"])));

    // Verify if already in DB
    if (checkDatabaseExistence($connect, 'computer', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => "Line $line: The computer with serial ".$attr["SERIAL"]." is already in the database"];
    }

    //  --------------- Verify Manufacturer ---------------
    if (!checkDatabaseExistence($connect, 'manufacturer', 'name', $attr["MANUFACTURER"])) {
        return ["state" => false, "message" => "Line $line: The Manufacturer ".$attr["MANUFACTURER"]." isn't registered"];
    }

    //   --------------- Verify OS ---------------
    if (!checkDatabaseExistence($connect, 'operating_system', 'name', $attr["OS"])) {
        return ["state" => false, "message" => "Line $line: The OS ".$attr["OS"]." isn't registered"];
    }

    //  --------------- Verify Type ---------------
    if (!checkDatabaseExistence($connect, 'computer_type', 'name', $attr["TYPE"])) {
        return ["state" => false, "message" => "Line $line: The computer type ".$attr["TYPE"]." isn't registered"];
    }

    // SQL INSERT REQUEST
    $request_add_computer = "INSERT INTO computer (serial_number, name, model, cpu, ram_mb, disk_gb, domain, location, building, room, mac_address, purchase_date, warranty_end, manufacturer_name, os_name, type_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_add_computer = mysqli_prepare($connect, $request_add_computer);
    mysqli_stmt_bind_param($stmt_add_computer, "ssssssssssssssss", 
        $attr["SERIAL"], 
        $attr["NAME"], 
        $attr["MODEL"], 
        $attr["CPU"], 
        $attr["RAM_MB"], 
        $attr["DISK_GB"], 
        $attr["DOMAIN"], 
        $attr["LOCATION"], 
        $attr["BUILDING"], 
        $attr["ROOM"], 
        $attr["MACADDR"], 
        $attr["PURCHASE_DATE"], 
        $attr["WARRANTY_END"], 
        $attr["MANUFACTURER"], 
        $attr["OS"], 
        $attr["TYPE"]
    );
    
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