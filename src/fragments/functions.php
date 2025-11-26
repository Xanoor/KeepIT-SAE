<?php 

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
    $html = "<table><tr>";
    foreach ($result as $value) {
        $html .= "<th>" . htmlspecialchars($value) . "</th>";
    }
    $html .= "</tr>";

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
    $html .= "</table>";

    return $html;
}

?>