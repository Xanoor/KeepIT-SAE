<?php 

require_once 'db.php';
require_once '../fragments/computer-details.php';
require_once '../fragments/monitor-details.php';

// Used to convert text to french (language used for this web site)
function convertDataToFrench($data) {
    $translations = [
        "Monitor" => "Écrans",
        "Computer" => "Unités centrales"
    ];
    return $translations[$data] ?? $data;
}

/**
 * Check if a table exists in the database.
 * 
 * @param mysqli $connect Database connection.
 * @param string $table The table name.
 * @return bool True if the table exists, false otherwise.
 */
function tableExists($conn, $table) {
    $table = mysqli_real_escape_string($conn, $table);
    $query = "SHOW TABLES LIKE '$table'";

    $res = mysqli_query($conn, $query);
    return mysqli_num_rows($res) > 0;
}


/**
 * Check if multiple columns exist in a specific table.
 * 
 * @param mysqli $connect Database connection.
 * @param string $table The table name.
 * @param array $columns List of column names to check.
 * @return bool True if all columns exist, false otherwise.
 */
function columnsExists($connect, $table, $columns) {
    $res = mysqli_query($connect, "DESCRIBE `$table`");
    if (!$res) return false;

    $existing = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $existing[] = $row['Field'];
    }

    return empty(array_diff($columns, $existing));
}


/**
 * Returns a CSS class name based on the item state by fetching it from the database.
 * 
 * @param string $state The state value from database.
 * @return string The CSS class from device_states table.
 */
function getStateClass($state) {
    global $connect;
    static $stateCache = null;

    // Load all states once per request to avoid N+1 queries
    if ($stateCache === null) {
        $stateCache = [];
        $query = "SHOW TABLES LIKE 'device_states'";
        $exists = mysqli_query($connect, $query);
        
        if (mysqli_num_rows($exists) > 0) {
            $res = mysqli_query($connect, "SELECT state, css_class FROM device_states");
            while ($row = mysqli_fetch_assoc($res)) {
                $stateCache[$row['state']] = $row['css_class'];
            }
        }
    }
    
    return $stateCache[$state] ?? "table-item-UNKNOWN";
}

/**
 * Generate HTML inputs for inventory filters based on distinct values in lookup tables.
 * 
 * @param array $tableList Associative array defining lookup rules.
 *              Format: [ "tableName" => "columnToFetch" ] 
 *              OR [ "tableName" => ["columnName" => "colToFetch", "devices_col" => "targetColInDevices"] ]
 *              - "columnName": The column in the lookup table containing the filter values.
 *              - "devices_col": (Optional) The database column name in the 'devices' table that this filter applies to.
 *                               Defaults to 'columnName' if not provided.
 * @return string HTML chunk of checkbox inputs.
 */
function generateInventoryFilters($tableList) {
    global $connect;
    // Get currently active filters from POST, default to "Tout selectionner" if empty
    $activeFilters = $_POST['filters'] ?? ['Tout selectionner'];
    $html = "";

    foreach ($tableList as $table => $config) {
        // Support both simple string (column name) or array configuration [columnName, devices_col]
        $lookupColumn = is_array($config) ? ($config['columnName'] ?? $config[0]) : $config;
        $devicesCol = is_array($config) ? ($config['devices_col'] ?? $config[1] ?? $lookupColumn) : $config;

        // mysqli_real_escape_string is used to prevent SQL injection (it escapes special characters like quotes)
        $safeTable = mysqli_real_escape_string($connect, $table);
        $safeColumn = mysqli_real_escape_string($connect, $lookupColumn);
        $query = "SELECT DISTINCT `$safeColumn` FROM `$safeTable` ORDER BY `$safeColumn` ASC";
        $result = mysqli_query($connect, $query);

        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $val = $row[0]; // ex, "Monitor", "Computer", "En stock"...

                if (is_null($val) || $val === "") continue;

                $safeVal = htmlspecialchars($val);
                $label = htmlspecialchars(convertDataToFrench($val)); // Translate DB value to French
                $id = "select-checkbox-" . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $val));
                
                // Determine if this specific checkbox should be checked
                $isChecked = false;
                
                // If "Tout selectionner" is active, everything should be visually checked
                if (in_array("Tout selectionner", $activeFilters)) {
                    $isChecked = true;
                } 
                // Otherwise, check if this specific value was selected in its column group
                else if (isset($activeFilters[$devicesCol]) && is_array($activeFilters[$devicesCol])) {
                    if (in_array($val, $activeFilters[$devicesCol])) {
                        $isChecked = true;
                    }
                }
                
                $checkedResult = $isChecked ? "checked" : "";

                // Generate HTML for the filter item
                $html .= "
                <div>
                    <label for=\"$id\">
                        $label
                    </label>
                    <input
                        name=\"filters[$devicesCol][]\"
                        type=\"checkbox\"
                        class=\"select-checkbox-checkbox\"
                        id=\"$id\"
                        value=\"$safeVal\"
                        $checkedResult
                    />
                </div>\n";

            }
        }
    }

    return $html;
}


/**
 * Create an HTML table from a CSV file.
 *
 * @param resource $file File pointer to the CSV file.
 * @param int $limit Maximum number of rows to read (default 100).
 * @return string HTML table.
 */
function importCSVTableBuilder($file, $limit = 100) {
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
 * Create an HTML table from a SQL query with pagination.
 *
 * @param string $tableName The table to fetch from.
 * @param array $filters Associative array of column => value for the WHERE clause.
 * @param int $start The starting index (offset).
 * @param int $end The ending index (limit = end - start).
 * @return string HTML table.
 */
function importSQLTableBuilder($tableName, $filters = [], $start = 0, $end = 11) {
    global $connect;

    $step = 11;
    
    // Validation of start and end
    if ($start < 0) $start = 0;
    if ($end <= $start) $end = $start + $step; // Default range if invalid

    
    // mysqli_real_escape_string is used to prevent SQL injection (add backslashes before special characters)
    $safeTable = mysqli_real_escape_string($connect, $tableName);
    // Build Base Query and Filter Clause
    // $filters format: [ "col" => ["val1", "val2"] ] (IN) OR [ "col" => "%val%" ] (LIKE) OR [ "col" => "val" ]
    $whereClause = "";
    if (!empty($filters)) {
        $filterParts = [];
        foreach ($filters as $column => $value) {
            $safeColumn = mysqli_real_escape_string($connect, $column);
            
            if (is_array($value)) {
                // Handle "IN" for multiple values
                $escapedValues = array_map(function($v) use ($connect) {
                    return "'" . mysqli_real_escape_string($connect, $v) . "'";
                }, $value);
                $filterParts[] = "`$safeColumn` IN (" . implode(", ", $escapedValues) . ")";
            } else if (strpos((string)$value, '%') !== false) { //strpos = Find the position of the first occurrence of a substring in a string
                // Handle LIKE for searches
                $safeValue = mysqli_real_escape_string($connect, $value);
                $filterParts[] = "`$safeColumn` LIKE '$safeValue'";
            } else {
                // Default 
                $safeValue = mysqli_real_escape_string($connect, $value);
                $filterParts[] = "`$safeColumn` = '$safeValue'";
            }
        }
        $whereClause = " WHERE " . implode(" AND ", $filterParts); //implode = Join array elements with a string
    }

    // Check Total Count to prevent "too high" values
    $countQuery = "SELECT COUNT(*) as total FROM `$safeTable`" . $whereClause;
    $countResult = mysqli_query($connect, $countQuery);
    $totalRows = mysqli_fetch_assoc($countResult)['total'];

    if ($start >= $totalRows) {
        return "<p>Aucune donnée trouvée (Index de départ trop élevé).</p>";
    }

    // Adjust end if it's too high
    if ($end > $totalRows) {
        $end = $totalRows;
    }

    $limit = $end - $start;

    // Final Query with LIMIT
    $query = "SELECT * FROM `$safeTable`" . $whereClause . " LIMIT $start, $limit";
    $result = mysqli_query($connect, $query);

    if (!$result) {
        return "<p>Erreur : " . htmlspecialchars(mysqli_error($connect)) . "</p>";
    }

    // Header
    $html = "<table><thead><tr>";
    $fields = mysqli_fetch_fields($result);
    
    if (empty($fields)) {
        return "<p>Aucune donnée trouvée.</p>";
    }

    foreach ($fields as $field) {
        $html .= "<th>" . htmlspecialchars(str_replace('_', ' ', strtoupper($field->name))) . "</th>";
    }

    $serialNumber = null;
    $deviceType = null;

    $html .= "<th>ACTION</th>";
    $html .= "</tr></thead><tbody>";

    // Body
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "<tr>";
        foreach ($row as $colName => $value) {
            $content = htmlspecialchars($value ?? '');
            if ($colName === 'state') {
                $content = "<span class=\"" . getStateClass($value) . "\">" . $content . "</span>";
            } else if ($colName === 'device_type') {
                $deviceType = $content;
            } else if ($colName === 'serial_number') {
                $serialNumber = $content;
            }
            $html .= "<td>" . $content . "</td>";
        }

        if ($serialNumber != null && $deviceType != null) {
            $html .= "<td>   
                        <a href='./inventory-item.php?serialNumber=".$serialNumber."&deviceType=".$deviceType."'>
                            <img
                                src='../assets/open.png'
                                alt='Ouvrir'
                            />
                        </a>
                    </td>"; 
        } else {
            $html .= "<td>None</td>";
        }

        $html .= "</tr>";
    }
    $html .= "</tbody></table>";

    return $html;
}

function getTableValues($table, $column) {
    global $connect;

    if (!tableExists($connect, $table)) {
        return [];
    }

    if (!columnsExists($connect, $table, [$column])) {
        return [];
    }

    $query = "SELECT {$column} FROM {$table}";
    $result = mysqli_query($connect, $query);

    if (!$result) {
        return [];
    }

    $values = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $values[] = $row[$column];
    }

    return $values;
}

/**
 * Generates the HTML fragment for a computer's details or creation form.
 * 
 * Fetches required lookup values (states, OS, manufacturers, locations) from the database 
 * and selects the current values based on the provided $items array.
 * 
 * @param array $items Associative array of computer data (can be empty for new items).
 * @param bool $new_item If true, adjusts the fragment for creating a new item instead of editing.
 * @return string The rendered HTML fragment.
 */
function createComputerPage($items, $new_item=false) {

    $state_list = getTableValues("device_states", "state");
    $state_html = "";
    foreach ($state_list as $index => $value) {
        $selected = ($value == ($items["state"] ?? "")) ? " selected" : "";
        $state_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    $os_list = getTableValues("operating_system", "name");
    $os_html = "";
    foreach ($os_list as $index => $value) {
        $selected = ($value == ($items["os_name"] ?? "")) ? " selected" : "";
        $os_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    $manufacturer_list = getTableValues("manufacturer", "name");
    $manufacturer_html = "";
    foreach ($manufacturer_list as $index => $value) {
        $selected = ($value == ($items["manufacturer_name"] ?? "")) ? " selected" : "";
        $manufacturer_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    $location_list = getTableValues("locations", "location");
    $location_html = "";
    foreach ($location_list as $index => $value) {
        $selected = ($value == ($items["location"] ?? "")) ? " selected" : "";
        $location_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    return computerDetailsFragment($items, $state_html, $os_html, $manufacturer_html, $location_html, $new_item);
}

/**
 * Generates the HTML fragment for a monitor's details or creation form.
 * 
 * Fetches required lookup values (states, manufacturers, connectors, attached computers) from the database 
 * and selects the current values based on the provided $items array.
 * 
 * @param array $items Associative array of monitor data (can be empty for new items).
 * @param bool $new_item If true, adjusts the fragment for creating a new item instead of editing.
 * @return string The rendered HTML fragment.
 */
function createMonitorPage($items, $new_item=false) {

    $state_list = getTableValues("device_states", "state");
    $state_html = "";
    foreach ($state_list as $index => $value) {
        $selected = ($value == ($items["state"] ?? "")) ? " selected" : "";
        $state_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    $manufacturer_list = getTableValues("manufacturer", "name");
    $manufacturer_html = "";
    foreach ($manufacturer_list as $index => $value) {
        $selected = ($value == ($items["manufacturer_name"] ?? "")) ? " selected" : "";
        $manufacturer_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    $connector_list = getTableValues("connector", "name");
    $connector_html = "";
    foreach ($connector_list as $index => $value) {
        $selected = ($value == ($items["connector_name"] ?? "")) ? " selected" : "";
        $connector_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    $attached_to_list = getTableValues("computer", "serial_number");
    $attached_to_html = "";
    foreach ($attached_to_list as $index => $value) {
        $selected = ($value == ($items["attached_to_serial"] ?? "")) ? " selected" : "";
        $attached_to_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    return monitorDetailsFragment($items, $state_html, $manufacturer_html, $connector_html, $attached_to_html, $new_item);
}

function loadInventoryItem($serialNumber, $deviceType) {
    global $connect;


    if (!tableExists($connect, "device_types") || !tableExists($connect, strtolower($deviceType))) {
        return null;
    }

    if (!columnsExists($connect, "device_types", ["name"])) {
        return null;
    }

    if (!checkDatabaseExistence($connect, "device_types", "name", $deviceType)) {
        return null;
    }

    $query = "SELECT * FROM devices d,".strtolower($deviceType)." item 
            WHERE d.serial_number = ? 
            AND d.serial_number = item.serial_number";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $serialNumber);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);

    switch (strtolower($deviceType)) {
        case 'computer':
            // serial number isn't in devices & computer database
            if (!checkDatabaseExistence($connect, "devices", "serial_number", $serialNumber) || !checkDatabaseExistence($connect, "computer", "serial_number", $serialNumber))
                return null;
            return createComputerPage($data);
            break;
        case 'monitor':
            // serial number isn't in devices & monitor database
            if (!checkDatabaseExistence($connect, "devices", "serial_number", $serialNumber) || !checkDatabaseExistence($connect, "monitor", "serial_number", $serialNumber))
                return null;
            return createMonitorPage($data);
            break;
        default:
            return null;
            break;
    }

    return null;
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

    $attr['STATE'] ??= 'En stock';

    // Verify if already in DB
    if (checkDatabaseExistence($connect, 'monitor', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => "Line $line: The monitor with serial ".$attr["SERIAL"]." is already in the database"];
    }
    if (checkDatabaseExistence($connect, 'devices', 'serial_number', $attr["SERIAL"])) {
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

    // Verify Type
    if (!checkDatabaseExistence($connect, 'device_types', 'name', "Monitor")) {
        return ["state" => false, "message" => "Line $line: The monitor type ".$attr["TYPE"]." isn't registered"];
    }

     // Verify attached to computer
     if (!empty($attr["ATTACHED_TO"])) {
        if (!checkDatabaseExistence($connect, 'computer', 'serial_number', $attr["ATTACHED_TO"])) {
            return ["state" => false, "message" => "Line $line: The computer ".$attr["ATTACHED_TO"]." isn't registered"];
        }
     } else {
         $attr["ATTACHED_TO"] = null; // set to null
     }

    // If the 2 inserts are not done, cancel ALL inserts otherwise continue
    mysqli_begin_transaction($connect);
    try {
        // Insert into DEVICES first (Parent)
        $req_devices = "INSERT INTO devices (serial_number, device_type, model, state) VALUES (?, 'Monitor', ?, ?)";
        $stmt_dev = mysqli_prepare($connect, $req_devices);
        mysqli_stmt_bind_param($stmt_dev, "sss", 
            $attr["SERIAL"], $attr["MODEL"], $attr["STATE"]
        );
        
        if (!mysqli_stmt_execute($stmt_dev)) {
            throw new Exception("Error inserting into devices: " . mysqli_stmt_error($stmt_dev));
        }
        // Insert into MONITOR (Child)
        $req_monitor = "INSERT INTO monitor (serial_number, size_inch, resolution, manufacturer_name, connector_name, attached_to_serial) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_monitor = mysqli_prepare($connect, $req_monitor);
        mysqli_stmt_bind_param($stmt_monitor, "ssssss", 
            $attr["SERIAL"], $attr["SIZE_INCH"], $attr["RESOLUTION"], $attr["MANUFACTURER"], 
            $attr["CONNECTOR"], $attr["ATTACHED_TO"]
        );
        if (!mysqli_stmt_execute($stmt_monitor)) {
            throw new Exception("Error inserting into monitor: " . mysqli_stmt_error($stmt_monitor));
        }
        // Everything worked
        mysqli_commit($connect);
        return ["state" => true];
    } catch (Exception $e) {
        // Something failed -> Undo everything
        mysqli_rollback($connect);
        return ["state" => false, "message" => "Line $line: " . $e->getMessage()];
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

    $attr['STATE'] ??= 'En stock';

    // Verify if already in DB
    if (checkDatabaseExistence($connect, 'computer', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => "Line $line: The computer with serial ".$attr["SERIAL"]." is already in the database"];
    }
    if (checkDatabaseExistence($connect, 'devices', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => "Line $line: An item with serial ".$attr["SERIAL"]." is already in the database"];
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
    if (!checkDatabaseExistence($connect, 'device_types', 'name', 'Computer')) {
        return ["state" => false, "message" => "Line $line: The computer type ".$attr["TYPE"]." isn't registered"];
    }

    //  --------------- Verify location ---------------
    if (!checkDatabaseExistence($connect, 'locations', 'location', $attr['LOCATION'])) {
        return ["state" => false, "message" => "Line $line: The location ".$attr["LOCATION"]." isn't registered"];
    }

    //  --------------- Verify state ---------------
    if (!checkDatabaseExistence($connect, 'device_states', 'state', $attr['STATE'])) {
        return ["state" => false, "message" => "Line $line: The state ".$attr["STATE"]." isn't registered"];
    }

    // If the 2 inserts are not done, cancel ALL inserts otherwise continue
    mysqli_begin_transaction($connect);
    try {
        // Insert into DEVICES first (Parent)
        $req_devices = "INSERT INTO devices (serial_number, device_type, model, state) VALUES (?, 'Computer', ?, ?)";
        $stmt_dev = mysqli_prepare($connect, $req_devices);
        mysqli_stmt_bind_param($stmt_dev, "sss", 
            $attr["SERIAL"], $attr["MODEL"], $attr["STATE"]
        );
        
        if (!mysqli_stmt_execute($stmt_dev)) {
            throw new Exception("Error inserting into devices: " . mysqli_stmt_error($stmt_dev));
        }
        // Insert into COMPUTER (Child)
        $req_comp = "INSERT INTO computer (serial_number, name, location, building, room, cpu, ram_mb, disk_gb, domain, mac_address, purchase_date, warranty_end, manufacturer_name, os_name, type_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_comp = mysqli_prepare($connect, $req_comp);
        mysqli_stmt_bind_param($stmt_comp, "sssssssssssssss", 
            $attr["SERIAL"], $attr["NAME"], $attr["LOCATION"], $attr["BUILDING"], $attr["ROOM"], $attr["CPU"], $attr["RAM_MB"], $attr["DISK_GB"], 
            $attr["DOMAIN"], $attr["MACADDR"], $attr["PURCHASE_DATE"], $attr["WARRANTY_END"], 
            $attr["MANUFACTURER"], $attr["OS"], $attr["TYPE"]
        );
        if (!mysqli_stmt_execute($stmt_comp)) {
            throw new Exception("Error inserting into computer: " . mysqli_stmt_error($stmt_comp));
        }
        // Everything worked
        mysqli_commit($connect);
        return ["state" => true];
    } catch (Exception $e) {
        // Something failed -> Undo everything
        mysqli_rollback($connect);
        return ["state" => false, "message" => "Line $line: " . $e->getMessage()];
    }
}

?>