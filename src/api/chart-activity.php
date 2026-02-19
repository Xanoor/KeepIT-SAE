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

$days   = 14;
$labels = [];
$data   = [];

for ($i = $days - 1; $i >= 0; $i--) {
    $day          = date('Y-m-d', strtotime("-$i days"));
    $labels[]     = $day;
    $data[$day]   = 0;
}

if (tableExists($connect, 'device_logs')) {
    $query  = "SELECT DATE(log_date) AS day, COUNT(*) AS total
               FROM device_logs
               WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
               GROUP BY day
               ORDER BY day ASC";
    $result = mysqli_query($connect, $query);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($data[$row['day']])) {
                $data[$row['day']] = (int) $row['total'];
            }
        }
    }
}

echo json_encode(['labels' => $labels, 'data' => array_values($data)]);
