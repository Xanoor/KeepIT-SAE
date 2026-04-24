<?php 

require_once 'db.php';
require_once '../fragments/computer-details.php';
require_once '../fragments/monitor-details.php';

/**
 * Converts specific English device-related terms to French.
 * 
 * @param string $data The text to convert.
 * @return string The translated text if found, or the original text.
 */
function convertDataToFrench($data) {
    $data_edit = strtoupper($data);

    $translations = [
        "DEVICES" => "APPAREILS",
        "MONITOR" => "Écrans",
        "COMPUTER" => "Ordinateur",
        "SERIAL NUMBER" => "Numéro de série",
        "MODEL" => "Modèle",
        "DEVICE TYPE" => "Type d'appareil",
        "CREATED AT" => "Créé le",
        "UPDATED AT" => "Modifié le",
        "STATE" => "État",
        "TECHNICIAN" => "Technicien",
        "WEB ADMINISTRATOR" => "Administrateur Web",
        "SYSTEM ADMINISTRATOR" => "Administrateur Système",
    ];
    return $translations[$data_edit] ?? $data;
}

/**
 * Formats a datetime string into a human-readable "time ago" format in French.
 * @param string|null $datetime The datetime string to format (e.g., "2024-06-01 12:00:00").
 * @return string A human-readable string representing how long ago the datetime was (e.g., "Il y a 2 jours"). Returns "Jamais" if the input is null or empty, and "Maintenant" if the datetime is within the last minute.
 */
function timeAgoFr(?string $datetime): string {
    if (empty($datetime)) return "Jamais";

    $now  = new DateTime();
    $past = new DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) return "Il y a " . $diff->y . " an" . ($diff->y > 1 ? "s" : "");
    if ($diff->m > 0) return "Il y a " . $diff->m . " mois";
    if ($diff->d > 0) return "Il y a " . $diff->d . " jour" . ($diff->d > 1 ? "s" : "");
    if ($diff->h > 0) return "Il y a " . $diff->h . " heure" . ($diff->h > 1 ? "s" : "");
    if ($diff->i > 0) return "Il y a " . $diff->i . " minute" . ($diff->i > 1 ? "s" : "");
    return "Maintenant";
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
 * Retrieves a list of column names for a given table.
 * 
 * @param mysqli|null $connect Database connection.
 * @param string $table The table name.
 * @return array|false An array of column names if successful, false otherwise.
 */
function getColumns($connect, $table) {
    if (!$connect) global $connect;

    $res = mysqli_query($connect, "DESCRIBE `$table`");
    if (!$res) return false;

    $existing = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $existing[] = $row['Field'];
    }

    return $existing;
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
    $existing = getColumns($connect, $table);

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
        $html .= "<th>" . htmlspecialchars(convertDataToFrench($value)) . "</th>";
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
 * Builds an SQL WHERE clause string based on an array of filters.
 *
 * $filters format: 
 * - [ "col" => ["val1", "val2"] ] (generates IN clause)
 * - [ "col" => "%val%" ] (generates LIKE clause) 
 * - [ "col" => "val" ] (generates = clause)
 * 
 * @param array $filters Associative array of column => value(s) for the WHERE clause.
 * @return string The generated WHERE clause or an empty string if no filters.
 */
function buildSQLWhereClause($filters) {
    global $connect;
    $whereClause = "";
    if (!empty($filters)) {
        $filterParts = [];
        $likeFilters = [];
        foreach ($filters as $column => $value) {
            $safeColumn = mysqli_real_escape_string($connect, $column);
            
            if (is_array($value)) {
                // 'IN' is used when we have a list of possible values (e.g. ['Computer', 'Monitor'])
                $escapedValues = array_map(function($v) use ($connect) {
                    return "'" . mysqli_real_escape_string($connect, $v) . "'";
                }, $value);

                $filterParts[] = "`$safeColumn` IN (" . implode(", ", $escapedValues) . ")";

            } else if (strpos((string)$value, '%') !== false) { //strpos = Find the position of the first occurrence of a su
                // '%' means we're doing a partial search (e.g. search for serial numbers containing 'ABC')
                $safeValue = mysqli_real_escape_string($connect, $value);
                $likeFilters[] = "`$safeColumn` LIKE '$safeValue'";

            } else {
                // Simple equality for single values
                $safeValue = mysqli_real_escape_string($connect, $value);
                $filterParts[] = "`$safeColumn` = '$safeValue'";
            }
        }
        //Join all parts with 'AND' so that all conditions must be met
        //implode = Join array elements with a string
        $whereClause = " WHERE " . implode(" AND ", $filterParts);
        
        if (!empty($likeFilters)) {
            if (!empty($filterParts))
                $whereClause .= " AND (" . implode(" OR ", $likeFilters) . ")";
            else
                $whereClause .= implode(" OR ", $likeFilters);
        }
    }
    return $whereClause;
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
function importSQLTableBuilder($tableName, $filters = [], $start = 0, $end = 11, $attr = []) {
    global $connect;

    $step = 11; // Default number of items per page
    
    // Validation of start and end
    if ($start < 0) $start = 0;
    if ($end <= $start) $end = $start + $step; // Default range if invalid

    // We use mysqli_real_escape_string to protect against SQL Injection
    // It neutralizes special characters that could break the query or allow unauthorized access
    $safeTable = mysqli_real_escape_string($connect, $tableName);
    // Build Base Query and Filter Clause
    // $filters format: [ "col" => ["val1", "val2"] ] (IN) OR [ "col" => "%val%" ] (LIKE) OR [ "col" => "val" ]
    $whereClause = buildSQLWhereClause($filters);

    if (empty($attr)) $attributes = "*"; 
    else $attributes = implode(", ", $attr);

    // We check the total number of rows matching the filters to ensure pagination index is valid
    $countQuery = "SELECT COUNT(*) as total FROM `$safeTable`" . $whereClause;
    $countResult = mysqli_query($connect, $countQuery);
    $totalRows = mysqli_fetch_assoc($countResult)['total'];

    if ($start >= $totalRows) {
        return "<p>Aucune donnée trouvée (Index de départ trop élevé).</p>";
    }

    // If 'end' exceeds total rows, we cap it to the maximum available
    if ($end > $totalRows) {
        $end = $totalRows;
    }

    $limit = $end - $start;

    // We order by 'updated_at' so the user sees recent changes first
    $query = "SELECT ".$attributes." FROM `$safeTable`".$whereClause." ORDER BY updated_at DESC LIMIT $start, $limit";
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

    // Draw headers
    foreach ($fields as $field) {
        // We format column names to be prettier (uppercase, no underscores, translated)
        $html .= "<th>" . htmlspecialchars(convertDataToFrench(str_replace('_', ' ', strtoupper($field->name)))) . "</th>";
    }

    $html .= "<th>ACTION</th>";
    $html .= "</tr></thead><tbody>";

    // Draw rows
    while ($row = mysqli_fetch_assoc($result)) {
        $html .= "<tr>";
        
        $serialNumber = null;
        $deviceType = null;

        foreach ($row as $colName => $value) {
            $content = htmlspecialchars($value ?? '');

            // Specific formatting for the 'state' column (adding CSS classes for colors)
            if ($colName === 'state') {
                $content = "<span class=\"" . getStateClass($value) . "\">" . $content . "</span>";
            } 
            
            // We store these to build the 'Action' link later
            if ($colName === 'device_type') {
                $deviceType = $content;
            } else if ($colName === 'serial_number') {
                $serialNumber = $content;
            }

            $html .= "<td>" . $content . "</td>";
        }

        // Action column: create a link to view item details
        if ($serialNumber != null && $deviceType != null) {
            $html .= "<td>   
                        <a href='./inventory-item.php?serialNumber=".$serialNumber."&deviceType=".$deviceType."'>
                            <img src='../assets/open.png' alt='Ouvrir' />
                        </a>
                    </td>"; 
        } else {
            $html .= "<td>Aucun</td>";
        }

        $html .= "</tr>";
    }

    $html .= "</tbody></table>";

    return $html;
}

/**
 * Fetches all unique values from a specific column in a database table.
 * 
 * @param string $table The name of the table to query.
 * @param string $column The name of the column to fetch values from.
 * @return array An array containing all values found in the column.
 */
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

    $attached_to_list = getTableValues("computer", "name");
    $attached_to_html = "";
    foreach ($attached_to_list as $index => $value) {
        $selected = ($value == ($items["attached_to_computer"] ?? "")) ? " selected" : "";
        $attached_to_html .= "<option value='{$value}'{$selected}>{$value}</option>";
    }

    return monitorDetailsFragment($items, $state_html, $manufacturer_html, $connector_html, $attached_to_html, $new_item);
}

/**
 * Loads a specific inventory item's details and renders the appropriate HTML fragment (computer or monitor).
 * 
 * @param string $serialNumber The serial number of the device to load.
 * @param string $deviceType The type of device ('Computer' or 'Monitor').
 * @return string|null The rendered HTML fragment or null if the item or table doesn't exist.
 */
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
 * 
 * @param array $attr The associative array to check.
 * @param array $required_keys List of keys that must be present.
 * @param int|string $line Line number for error reporting.
 * @return array Returns ['state' => true] on success, or ['state' => false, 'message' => '...'] on failure.
 */
function verifyRequiredKeys($attr, $required_keys, $line) {
    $required_keys_model = array_flip($required_keys);
    $missing_keys = array_diff_key($required_keys_model, $attr);
    if (!empty($missing_keys)) { 
        return ["state" => false, "message" => "Ligne $line : Clés manquantes."];
    }
    return ["state" => true];
}

/**
 * Check if a value exists in a specific table and column.
 * 
 * @param mysqli $connect Database connection.
 * @param string $table The table name.
 * @param string $column The column name.
 * @param mixed $value The value to search for.
 * @return bool True if the value exists, false otherwise.
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
 * Validates the technical attributes of a monitor against database constraints and lookup tables.
 * 
 * @param array $attr Associative array containing monitor attributes (MANUFACTURER, CONNECTOR, STATE, etc.).
 * @param string $lineText Prefix for error messages, typically identifying the CSV line number.
 * @return array Returns ['state' => true, 'attr' => array] on success, or ['state' => false, 'message' => '...'] on failure.
 */
function checkMonitorAttributes($attr, $lineText="") {
    global $connect;

    // Verify Manufacturer
    if (!checkDatabaseExistence($connect, 'manufacturer', 'name', $attr["MANUFACTURER"])) {
        return ["state" => false, "message" => $lineText."Le fabricant ".$attr["MANUFACTURER"]." n'est pas enregistré"];
    }

    // Verify Connector
    if ($attr["CONNECTOR"] != "null") {
        if (!checkDatabaseExistence($connect, 'connector', 'name', $attr["CONNECTOR"])) {
            return ["state" => false, "message" => $lineText."Le connecteur ".$attr["CONNECTOR"]." n'est pas enregistré"];
        }
    } else {
        $attr["CONNECTOR"] = null;
    }

    // Verify Type
    if (!checkDatabaseExistence($connect, 'device_types', 'name', "Monitor")) {
        return ["state" => false, "message" => $lineText."Le type d'appareil Monitor n'est pas enregistré"];
    }

    // Verify state 
    if (!checkDatabaseExistence($connect, 'device_states', 'state', $attr['STATE'])) {
        return ["state" => false, "message" => $lineText."Le statut ".$attr["STATE"]." n'est pas enregistré"];
    }

    // Verify attached to computer
    if (!empty($attr["ATTACHED_TO"])) {
        if (!checkDatabaseExistence($connect, 'computer', 'name', $attr["ATTACHED_TO"])) {
            return ["state" => false, "message" => $lineText."L'ordinateur ".$attr["ATTACHED_TO"]." n'est pas enregistré"];
        }
    } else {
        $attr["ATTACHED_TO"] = null; // set to null
    }

    // Constraints
    if (!is_numeric($attr["SIZE_INCH"]) || $attr["SIZE_INCH"] < 0) {
        return ["state" => false, "message" => $lineText."La taille de l'écran ".$attr["SIZE_INCH"]." n'est pas valide"];
    } else if (empty($attr["RESOLUTION"])) {
        return ["state" => false, "message" => $lineText."Le champ résolution ne peut pas être vide"];
    } else if (empty($attr["MODEL"])) {
        return ["state" => false, "message" => $lineText."Le champ modèle ne peut pas être vide"];
    }

    return ["state" => true, "attr" => $attr];
}

/**
 * Adds a new monitor to the database, ensuring data consistency across 'devices' and 'monitor' tables.
 * 
 * @param array $attr Associative array containing all required monitor attributes.
 * @param int|string|null $line Optional line number for error reporting (imported from CSV).
 * @return array Returns ['state' => true] on success, or ['state' => false, 'message' => '...'] on failure.
 */
function addMonitor($attr, $line = null) {
    global $connect;
    $lineText = "";
    if ($line) $lineText = "Line $line: ";

    $required_keys = ["SERIAL","MANUFACTURER","MODEL","SIZE_INCH","RESOLUTION","CONNECTOR","ATTACHED_TO"];
    
    $checkKeys = verifyRequiredKeys($attr, $required_keys, $line);
    if (!$checkKeys["state"]) return $checkKeys;

    $attr['STATE'] ??= 'En stock';

    // Verify if already in DB
    if (checkDatabaseExistence($connect, 'monitor', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => $lineText."L'écran avec le numéro de série ".$attr["SERIAL"]." est déjà dans la base de données"];
    }
    if (checkDatabaseExistence($connect, 'devices', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => $lineText."L'écran avec le numéro de série ".$attr["SERIAL"]." est déjà dans la base de données"];
    }

    $monitorAttributesCheck = checkMonitorAttributes($attr, $lineText);
    if (!$monitorAttributesCheck["state"]) {
        return $monitorAttributesCheck;
    }
    $attr = $monitorAttributesCheck["attr"];

    // If the 2 inserts are not done, cancel ALL inserts otherwise continue
    mysqli_begin_transaction($connect);
    try {
        // Insert into DEVICES first (Parent)
        $req_devices = "INSERT INTO devices (serial_number, device_type, model, manufacturer_name, state) VALUES (?, 'Monitor', ?, ?, ?)";
        $stmt_dev = mysqli_prepare($connect, $req_devices);
        mysqli_stmt_bind_param($stmt_dev, "ssss", 
            $attr["SERIAL"], $attr["MODEL"], $attr["MANUFACTURER"], $attr["STATE"]
        );
        
        if (!mysqli_stmt_execute($stmt_dev)) {
            throw new Exception("Erreur lors de l'insertion dans devices : " . mysqli_stmt_error($stmt_dev));
        }

        // Insert into MONITOR (Child)
        $req_monitor = "INSERT INTO monitor (serial_number, size_inch, resolution, connector_name, attached_to_computer) VALUES (?, ?, ?, ?, ?)";
        $stmt_monitor = mysqli_prepare($connect, $req_monitor);
        mysqli_stmt_bind_param($stmt_monitor, "sssss", 
            $attr["SERIAL"], $attr["SIZE_INCH"], $attr["RESOLUTION"], 
            $attr["CONNECTOR"], $attr["ATTACHED_TO"]
        );
        if (!mysqli_stmt_execute($stmt_monitor)) {
            throw new Exception("Erreur lors de l'insertion dans monitor : " . mysqli_stmt_error($stmt_monitor));
        }
        // Everything worked
        mysqli_commit($connect);
        return ["state" => true];
    } catch (Exception $e) {
        // Something failed -> Undo everything
        mysqli_rollback($connect);
        return ["state" => false, "message" => $lineText.$e->getMessage()];
    }
}

/**
 * Validates the technical attributes of a computer against database constraints and lookup tables.
 * 
 * @param array $attr Associative array containing computer attributes (MANUFACTURER, OS, LOCATION, etc.).
 * @param string $lineText Prefix for error messages, typically identifying the CSV line number.
 * @return array Returns ['state' => true, 'attr' => array] on success, or ['state' => false, 'message' => '...'] on failure.
 */
function checkComputerAttributes($attr, $lineText="") {
    global $connect;

    //  --------------- Verify Manufacturer ---------------
    if (!checkDatabaseExistence($connect, 'manufacturer', 'name', $attr["MANUFACTURER"])) {
        return ["state" => false, "message" => $lineText."Le fabricant ".$attr["MANUFACTURER"]." n'est pas enregistré"];
    }

    //   --------------- Verify OS ---------------
    if ($attr["OS"] != "null") {
        if (!checkDatabaseExistence($connect, 'operating_system', 'name', $attr["OS"])) {
            return ["state" => false, "message" => $lineText."Le système d'exploitation ".$attr["OS"]." n'est pas enregistré"];
        }
    } else {
        $attr["OS"] = null;
    }

    //  --------------- Verify Type ---------------
    if (!checkDatabaseExistence($connect, 'device_types', 'name', 'Computer')) {
        return ["state" => false, "message" => $lineText."Le type d'appareil Computer n'est pas enregistré"];
    }

    //  --------------- Verify location ---------------
    if (!checkDatabaseExistence($connect, 'locations', 'location', $attr['LOCATION'])) {
        return ["state" => false, "message" => $lineText."Le lieu ".$attr["LOCATION"]." n'est pas enregistré"];
    }

    //  --------------- Verify state ---------------
    if (!checkDatabaseExistence($connect, 'device_states', 'state', $attr['STATE'])) {
        return ["state" => false, "message" => $lineText."Le statut ".$attr["STATE"]." n'est pas enregistré"];
    }

    //  --------------- Verify name ---------------
    if (checkDatabaseExistence($connect, 'computer', 'name', $attr['NAME'])) {
        // Verify if the name is already taken by another computer or used by the actual computer
        $req_sn = "SELECT serial_number FROM computer WHERE name = ? AND serial_number = ?";
        $stmt = mysqli_prepare($connect, $req_sn);
        mysqli_stmt_bind_param($stmt, "ss", 
            $attr["NAME"], $attr["SERIAL"]
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $num_row = mysqli_num_rows($result);

        if ($num_row == 0 || $num_row > 1)
            return ["state" => false, "message" => $lineText."Le nom ".$attr["NAME"]." est déjà attribué"];
    }

    //  --------------- Verify MAC Addr ---------------
    if (checkDatabaseExistence($connect, 'computer', 'mac_address', $attr['MACADDR'])) {
        // Verify if the MAC addr is already taken by another computer or used by the actual computer
        $req_sn = "SELECT serial_number FROM computer WHERE mac_address = ? AND serial_number = ?";
        $stmt = mysqli_prepare($connect, $req_sn);
        mysqli_stmt_bind_param($stmt, "ss", 
            $attr["MACADDR"], $attr["SERIAL"]
        );
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $num_row = mysqli_num_rows($result);

        if ($num_row == 0 || $num_row > 1)
            return ["state" => false, "message" => $lineText."L'adresse MAC ".$attr["MACADDR"]." est déjà utilisée"];
    }

    //  --------------- Verify dates ---------------
    if (empty($attr["PURCHASE_DATE"])) {
        return ["state" => false, "message" => $lineText."Le date d'achat ne peut pas être vide"];
    }

    // Convert dates (to support different formats like DD/MM/YYYY)
    $attr["PURCHASE_DATE"] = date('Y-m-d', strtotime(str_replace('/', '-', $attr["PURCHASE_DATE"])));
    if (!empty($attr["WARRANTY_END"])) {
        $attr["WARRANTY_END"]  = date('Y-m-d', strtotime(str_replace('/', '-', $attr["WARRANTY_END"])));
    } else {
        $attr["WARRANTY_END"] = null;
    }
    
    // Constraints
    if (!is_numeric($attr["DISK_GB"]) || $attr["DISK_GB"] < 0) { // Disk size
        return ["state" => false, "message" => $lineText."La taille du disque ".$attr["DISK_GB"]." n'est pas valide."];
    } else if (!is_numeric($attr["RAM_MB"]) || $attr["RAM_MB"] < 0 ) { // RAM size
        return ["state" => false, "message" => $lineText."La taille de la RAM ".$attr["RAM_MB"]." n'est pas valide."];
    } else if (preg_match('/^[0-9A-Fa-f]{2}(:[0-9A-Fa-f]{2}){5}$/', $attr["MACADDR"]) !== 1) { // MAC Address
        return ["state" => false, "message" => $lineText."L'adresse MAC ".$attr["MACADDR"]." n'est pas valide."];
    } else if (empty($attr["CPU"])) {
        return ["state" => false, "message" => $lineText."Le champ Processeur ne peut pas être vide"];
    } else if ($attr["WARRANTY_END"] != null && $attr["WARRANTY_END"] < $attr["PURCHASE_DATE"]) {
        return ["state" => false, "message" => $lineText."La date de fin de garantie ne peut pas précéder la date d'achat"];
    } else if (empty($attr["MODEL"])) {
        return ["state" => false, "message" => $lineText."Le champ modèle ne peut pas être vide"];
    }

    return ["state" => true, "attr" => $attr];
}

/**
 * Adds a new computer to the database, ensuring data consistency across 'devices' and 'computer' tables.
 * 
 * @param array $attr Associative array containing all required computer attributes.
 * @param int|string|null $line Optional line number for error reporting (imported from CSV).
 * @return array Returns ['state' => true] on success, or ['state' => false, 'message' => '...'] on failure.
 */
function addComputer($attr, $line = null) {    
    global $connect;
    $lineText = "";
    if ($line) $lineText = "Line $line: ";
    
    // Verify if all attributs are in the dict
    $required_keys = [
        'NAME', 'SERIAL', 'MANUFACTURER', 'MODEL', 'TYPE', 'CPU', 
        'RAM_MB', 'DISK_GB', 'OS', 'DOMAIN', 'LOCATION', 'BUILDING', 
        'ROOM', 'MACADDR', 'PURCHASE_DATE', 'WARRANTY_END'
    ];
    
    $checkKeys = verifyRequiredKeys($attr, $required_keys, $line);
    if (!$checkKeys["state"]) return $checkKeys;

    $attr['STATE'] ??= 'En stock';

    // Verify if already in DB
    if (checkDatabaseExistence($connect, 'computer', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => $lineText."L'ordinateur avec le numéro de série ".$attr["SERIAL"]." est déjà dans la base de données"];
    }
    if (checkDatabaseExistence($connect, 'devices', 'serial_number', $attr["SERIAL"])) {
        return ["state" => false, "message" => $lineText."Un appareil avec le numéro de série ".$attr["SERIAL"]." est déjà dans la base de données"];
    }

    $checkComputerAttribute = checkComputerAttributes($attr, $lineText);
    if (!$checkComputerAttribute["state"]) {
        return $checkComputerAttribute;
    }
    $attr = $checkComputerAttribute["attr"];

    // If the 2 inserts are not done, cancel ALL inserts otherwise continue
    mysqli_begin_transaction($connect);
    try {
        // Insert into DEVICES first (Parent)
        $req_devices = "INSERT INTO devices (serial_number, device_type, model, manufacturer_name, state) VALUES (?, 'Computer', ?, ?, ?)";
        $stmt_dev = mysqli_prepare($connect, $req_devices);
        mysqli_stmt_bind_param($stmt_dev, "ssss", 
            $attr["SERIAL"], $attr["MODEL"], $attr["MANUFACTURER"], $attr["STATE"]
        );
        
        if (!mysqli_stmt_execute($stmt_dev)) {
            throw new Exception("Erreur lors de l'insertion dans devices : " . mysqli_stmt_error($stmt_dev));
        }

        // Insert into COMPUTER (Child)
        $req_comp = "INSERT INTO computer (serial_number, name, location, building, room, cpu, ram_mb, disk_gb, domain, mac_address, purchase_date, warranty_end, os_name, type_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_comp = mysqli_prepare($connect, $req_comp);
        mysqli_stmt_bind_param($stmt_comp, "ssssssssssssss", 
            $attr["SERIAL"], $attr["NAME"], $attr["LOCATION"], $attr["BUILDING"], $attr["ROOM"], $attr["CPU"], $attr["RAM_MB"], $attr["DISK_GB"], 
            $attr["DOMAIN"], $attr["MACADDR"], $attr["PURCHASE_DATE"], $attr["WARRANTY_END"], 
            $attr["OS"], $attr["TYPE"]
        );
        if (!mysqli_stmt_execute($stmt_comp)) {
            throw new Exception("Erreur lors de l'insertion dans computer : " . mysqli_stmt_error($stmt_comp));
        }
        // Everything worked
        mysqli_commit($connect);
        return ["state" => true];
    } catch (Exception $e) {
        // Something failed -> Undo everything
        mysqli_rollback($connect);
        return ["state" => false, "message" => $lineText . $e->getMessage()];
    }
}


/**
 * Fetches and formats the activity history logs for a specific device from the device_logs table.
 * 
 * @param string $serialNumber The serial number of the device to fetch logs for.
 * @return string HTML string containing formatted log items for the activity sidebar.
 */
function loadInventoryLogs($serialNumber) {
    global $connect;

    $query = "SELECT log_date, login, action_did, field_updated, old_val, new_val 
              FROM device_logs 
              WHERE serial_number = ? 
              ORDER BY log_date DESC";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $serialNumber);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $html = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $date = date('d-m-Y | H:i', strtotime($row['log_date']));
        $login = htmlspecialchars($row['login'] ?? '?');
        $action = $row['action_did'];
        $field = htmlspecialchars($row['field_updated']);
        $oldVal = htmlspecialchars($row['old_val'] ?? '');
        $newVal = htmlspecialchars($row['new_val'] ?? '');

        $text = "";
        switch ($action) {
            case 'INSERT':
                $text = "$login a ajouté l'appareil ($field - $serialNumber)";
                break;

            case 'UPDATE':
                $text = "$login a changé $field de \"$oldVal\" à \"$newVal\"";
                break;

            case 'DELETE':
                $text = "$login a supprimé l'appareil";
                break;

            case 'AT_DELETED':
                $text = "$login a supprimé l'ordinateur \"$oldVal\", qui était relié à cet écran.";
                break;
        }

        $html .= "
        <div class=\"activity-main-item\">
            <span class=\"activity-date\">$date</span>
            <span class=\"activity-text\">$text</span>
        </div>";
    }

    return $html;
}

function loadUsersLogs() {
    global $connect;

    $query = "SELECT log_date, login, action_did, old_val, new_val 
              FROM users_logs 
              ORDER BY log_date DESC";
    $result = mysqli_query($connect, $query);
    $html = "";

    if (!$result) {
        return $html;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $action_did = $row["action_did"];
        $log_date = $row["log_date"];
        $login = htmlspecialchars($row["login"]);
        $old_val = htmlspecialchars($row["old_val"]);
        $new_val = htmlspecialchars($row["new_val"]);

        switch ($action_did) {

            case 'TRY CONNECTION': //connexion failure
                $html .= "<p>{$log_date} - Échec de connexion pour l'utilisateur \"{$login}\".</p>";
                break;

            case 'CREATED': //account created
                $html .= "<p>{$log_date} - Création du compte \"{$login}\".</p>";
                break;

            case 'UPDATE FIRST NAME': //first name updated
                $html .= "<p>{$log_date} - Modification du prénom de \"{$login}\" : \"{$old_val}\" → \"{$new_val}\".</p>";
                break;

            case 'UPDATE LAST NAME': //last name updated
                $html .= "<p>{$log_date} - Modification du nom de \"{$login}\" : \"{$old_val}\" → \"{$new_val}\".</p>";
                break;

            case 'UPDATE PASSWORD': //password updated
                $html .= "<p>{$log_date} - Mot de passe modifié pour l'utilisateur \"{$login}\".</p>";
                break;

            case 'UPDATE ROLE': //role updated
                $html .= "<p>{$log_date} - Modification du rôle de \"{$login}\" : \"{$old_val}\" → \"{$new_val}\".</p>";
                break;

            case 'DELETED': //account deleted
                $html .= "<p>{$log_date} - Suppression du compte \"{$login}\".</p>";
                break;

            default: //else
                break;
        }
    }

    return $html;
}

function loadConstantLogs() {
    global $connect;

    $query = "SELECT log_date, table_name, action_did, val 
              FROM constant_logs 
              ORDER BY log_date DESC";
    $result = mysqli_query($connect, $query);
    $html = "";

    if (!$result) {
        return $html;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $action_did = $row["action_did"];
        $log_date = $row["log_date"];
        $table_name = htmlspecialchars($row["table_name"]);
        $val = htmlspecialchars($row["val"]);

        switch ($action_did) {

            case 'INSERT': //constant created
                $html .= "<p>{$log_date} - Nouvelle constante dans \"{$table_name}\": \"{$val}\".</p>";
                break;

            case 'DELETE': //constant removed
                $html .= "<p>{$log_date} - Constante supprimée dans \"{$table_name}\": \"{$val}\".</p>";
                break;

            default: //else
                break;
        }
    }

    return $html;
}

/**
 * Generates the HTML fragment for the export menu with columns grouped by table.
 * 
 * @param array $tables List of tables to include in the export menu.
 * @return string The rendered HTML fragment.
 */
function generateExportMenu($tables) {
    global $connect;

    $columns = [];
    foreach ($tables as $table) {
        $columns[convertDataToFrench($table)] = getColumns($connect, $table);
    }

    if (count($columns) === 0) {
        return '';
    }

    // column 0 reference
    $firstTable = array_key_first($columns);
    $column0 = $columns[$firstTable];

    // Remove duplicates from other tables
    foreach ($columns as $table => $cols) {
        if ($table === $firstTable) {
            continue;
        }

        $columns[$table] = array_values(array_diff($cols, $column0));
    }

    $html = include '../fragments/export-menu.php';
    return $html; 
}

/**
 * Fetches all users from the database matching a specific role.
 * 
 * @param string $role The role to filter users by.
 * @return mysqli_result|false The database query result set, or false on failure.
 */
function loadUsersFromDB($role) {
    global $connect;

    $query = "SELECT login, first_name, last_name, last_login_at, created_at 
              FROM users 
              WHERE role = ?";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return $result;
}

/**
 * Retrieves the login and hashed password for a specific user.
 * 
 * @param string $login The user's login name.
 * @return mysqli_result|false The database query result set containing the login and password hash, or false on failure.
 */
function getPasswordFromLogin($login) {
    global $connect;

    $query = "SELECT login, password_hash FROM users WHERE login = ?";

    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $login);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    return $result;
}

function createVariableConfig($tableName, $columnName) {    
    $varItems = getTableValues($tableName, $columnName);

    $html = include '../fragments/variable-settings.php';
    return $html; 
}
?>