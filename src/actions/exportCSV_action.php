
<?php
session_start();

// Only web admin and technician can access this action
if (!isset($_SESSION['login'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Web Administrator', 'Technician'])) {
    header("Location: ../pages/login.php");
    exit();
}

include_once '../includes/functions.php';

if (isset($_POST["export_submit"], $_POST["columns"]) && is_array($_POST["columns"])) {
    global $connect;
    $columns = $_POST["columns"];

    if (count($columns) <= 0) {
        $_SESSION['notification'] = "Vous n'avez pas sélectionner d'attributs.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/inventory.php");
        exit();
    }

    // Retrieve filters exactly as they were in the inventory page
    $filters = $_SESSION['inventory_export_filters'] ?? [];
    
    // Generate the SQL WHERE clause
    $whereQuery = buildSQLWhereClause($filters);

    // Verify if the columns selected can be exported (exist in the sql export view)
    if (!columnsExists($connect, "vw_export_inventaire", $columns)) {
        $_SESSION['notification'] = "Une ou plusieurs colonnes ne peuvent être exportées.";
        $_SESSION['notification_color'] = "red";
        header("Location: ../pages/inventory.php");
        exit();
    }

    $selectColumns = implode(", ", $columns);

    $sql = "SELECT ".$selectColumns." FROM vw_export_inventaire ".$whereQuery;
    $result = mysqli_query($connect, $sql);

    if ($result) {
        // Sometimes the buffer is not empty and creates blank rows: we clean it to avoid blank rows in the CSV
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Output headers to trigger CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="inventaire_export.csv"');
        
        // Use PHP's output stream
        $output = fopen('php://output', 'w');
        
        // Write the header row
        fputcsv($output, $columns);
        
        // Write the data rows
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit();
    } else {
        $_SESSION['notification'] = "Erreur: " . mysqli_error($connect);
        $_SESSION['notification_color'] = "red";
    }
} else {
    $_SESSION['notification'] = "Des éléments sont manquants pour effectuer l'export.";
    $_SESSION['notification_color'] = "red";
}

header("Location: ../pages/inventory.php");
exit();

?>