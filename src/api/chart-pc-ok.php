<?php
session_start();
header('Content-Type: application/json');

$ville = $_GET['ville'] ?? 'Vélizy';
$ram = $_GET['ram'] ?? 16384;
$disk = $_GET['disk'] ?? 512;

// On Appel l'API Plumber
$url = "http://localhost:8000/stat1?ville=" . urlencode($ville) . "&ram=$ram&disk=$disk";
$response = file_get_contents($url);

echo $response;