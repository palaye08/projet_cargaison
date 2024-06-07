
<?php 
  // Chemin du fichier JSON de sauvegarde
  $jsonFile = 'garde.json';

  // Vérifier si le fichier existe
  if (file_exists($jsonFile)) {
      // Lecture du fichier JSON
      $jsonData = file_get_contents($jsonFile);
      $cargaisons = json_decode($jsonData, true);
  
      // Vérifier si le décodage JSON a réussi
      if ($cargaisons === null && json_last_error() !== JSON_ERROR_NONE) {
          $error = "Erreur lors du décodage du JSON : " . json_last_error_msg();
      }
  } else {
      // Réponse en cas de fichier non trouvé
      $error = "Fichier non trouvé.";
  }
  
 ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit</title>
    <script src="http://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex h-screen bg-gray-400">
<div class="h-full w-80 bg-white">
    <h1 class="text-amber-950 flex items-center justify-center">DIOP TRANSIT</h1>
    <img class="rounded-full" src="download (1).jpeg" alt="">
    <div class="text-white h-10 w-30 m-2 hover:scale-110 rounded-lg flex items-center justify-center bg-amber-950"><a href="Cargaison.php">Cargaison</a></div>
    <div class="text-white h-10 w-30 m-2 hover:scale-110 rounded-lg flex items-center justify-center bg-amber-950"><a href="produit.php">Details</Details></a></div>
  </div>


  <div lass="bg-white p-2 rounded-lg mb-4">
  <div class="bg-white p-2 rounded-lg mb-4 relative top-20 left-[200px]">
<table class="min-w-full leading-normal">
    <thead>
        <tr class="text-left text-white bg-amber-950">
            <th class="px-4 py-2">Numéro</th>
            <th class="px-4 py-2">Type</th>
            <th class="px-4 py-2">Poids</th>
            <th class="px-4 py-2">Nombre de Produits</th>
            <th class="px-4 py-2">Date de Départ</th>
            <th class="px-4 py-2">Date d'Arrivée</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cargaisons as $cargaison) :?>
        <tr class="text-left text-black text-xl">
            <td class="px-4 py-2"><?php echo $cargaison['number'];?></td>
            <td class="px-4 py-2"><?php echo $cargaison['type'];?></td>
            <td class="px-4 py-2"><?php echo $cargaison['weight'];?></td>
            <td class="px-4 py-2"><?php echo $cargaison['products'];?></td>
            <td class="px-4 py-2"><?php echo $cargaison['dateDepart'];?></td>
            <td class="px-4 py-2"><?php echo $cargaison['dateArrivee'];?></td>
        </tr>
        <?php endforeach;?>
    </tbody>
   
</table>
</div>
</div>
</body>
</html>
