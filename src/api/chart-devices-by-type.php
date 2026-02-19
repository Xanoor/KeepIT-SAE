<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['login']) || !isset($_SESSION['role'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

require_once '../includes/db.php';
require_once '../includes/functions.php';

$labels = [];
$data   = [];

if (tableExists($connect, 'devices')) {
    $query  = "SELECT device_type, COUNT(*) AS total
               FROM devices
               GROUP BY device_type
               ORDER BY total DESC";
    $result = mysqli_query($connect, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $labels[] = $row['device_type'];
            $data[]   = (int) $row['total'];
        }
    }
}

echo json_encode(['labels' => $labels, 'data' => $data]);
