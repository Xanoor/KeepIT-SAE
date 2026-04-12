<?php
session_start();
header('Content-Type: application/json');

$debut = $_GET['debut'] ?? '2026-01-01';
$mois  = $_GET['mois']  ?? 18;

$url = "http://localhost:8000/stat2?debut=" . urlencode($debut) . "&mois=" . intval($mois);

// Appel de l'API R et récupération du résultat 
$response = file_get_contents($url);

echo $response
?>