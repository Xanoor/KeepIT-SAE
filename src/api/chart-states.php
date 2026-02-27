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

$labels  = [];
$data    = [];
$classes = [];

if (tableExists($connect, 'devices')) {
    $query  = "SELECT d.state, COUNT(*) AS total
               FROM devices d
               GROUP BY d.state
               ORDER BY total DESC";
    $result = mysqli_query($connect, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $labels[]  = $row['state'];
            $data[]    = (int) $row['total'];
            $classes[] = getStateClass($row['state']);
        }
    }
}

echo json_encode(['labels' => $labels, 'data' => $data, 'classes' => $classes]);
