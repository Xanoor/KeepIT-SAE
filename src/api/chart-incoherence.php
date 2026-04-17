<?php
session_start();
header('Content-Type: application/json');

$ram = $_GET['ram'] ?? 16384;
$ecran = $_GET['ecran'] ?? 27;

$url = "http://localhost:8000/stat4?ram=$ram&ecran=$ecran";
$response = file_get_contents($url);

echo $response;