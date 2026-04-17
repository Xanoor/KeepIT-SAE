<?php
session_start();

header('Content-Type: application/json'); // On prévient que c'est du JSON

// On appelle l'API Plumber
$url = "http://localhost:8000/stat3?paliers=10";
$response = file_get_contents($url);

echo $response;
?>