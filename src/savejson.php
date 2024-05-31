<?php
// Récupération des données envoyées par la requête POST
$cargoData = json_decode(file_get_contents('php://input'), true);

// Chemin du fichier JSON de sauvegarde
$jsonFile = 'garde.json';

// Lecture du fichier JSON actuel
$currentData = file_exists($jsonFile) ? json_decode(file_get_contents($jsonFile), true) : [];

// Ajout des nouvelles données à celles existantes
$currentData[] = $cargoData;

// Écriture des données dans le fichier JSON
file_put_contents($jsonFile, json_encode($currentData, JSON_PRETTY_PRINT));

// Réponse de succès
http_response_code(200);
var_dump($currentData);


?>
