<?php

if(isset($_REQUEST["ajouter"])){
    $newProd = json_decode(file_get_contents('php://input'), true);
    $data = json_decode(file_get_contents("garde.json"), true);
    $msg = "Produit non ajouter";
    foreach($data as $key => $cargo){
        if($cargo["number"] == $_REQUEST["ajouter"]){
            $data[$key]["produit"][] = $newProd;
            file_put_contents("garde.json", json_encode($data, JSON_PRETTY_PRINT));
            $msg = "Produit ajouter";
        }
    }
    echo $msg;
    

}else{
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

}
?>
