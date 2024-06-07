<?php



/* 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    $newProduct = $data['newProduct'];
    $currentCargoId = $data['currentCargoId'];

    // Chemin vers le fichier JSON
    $file_path = 'garde.json';

    // Lire le contenu actuel du fichier JSON
    $current_data = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];

    // Trouver la cargaison correspondante et ajouter le produit
    foreach ($current_data as &$cargo) {
        if ($cargo['number'] == $currentCargoId) {
            if (!isset($cargo['produit'])) {
                $cargo['produit'] = []; // Initialiser le tableau 'produit' s'il n'existe pas
            }
            $cargo['produit'] = $newProduct;
            break;
        }
    }

    // Sauvegarder les nouvelles données dans le fichier JSON
    file_put_contents($file_path, json_encode($current_data, JSON_PRETTY_PRINT));

    echo json_encode(["message" => "Données sauvegardées avec succès"]);
} else {
    echo json_encode(["message" => "Méthode non autorisée"]);
} */
?>
