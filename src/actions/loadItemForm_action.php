<?php

include_once '../includes/functions.php';

$allowed = ['COMPUTER', 'MONITOR'];

$type = $_GET['type'] ?? '';

if (!in_array($type, $allowed)) {
    http_response_code(400);
    exit;
}

if ($type === 'COMPUTER') {
    echo createComputerPage([], true);
} else if ($type === 'MONITOR') {
    echo createMonitorPage([], true);
}