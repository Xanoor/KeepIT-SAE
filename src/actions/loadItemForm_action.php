<?php
session_start();

// Only web admin and technician can access this action
if (!isset($_SESSION['login'], $_SESSION['role']) || !in_array($_SESSION['role'], ['Web Administrator', 'Technician'])) {
    header("Location: ../pages/login.php");
    exit();
}

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