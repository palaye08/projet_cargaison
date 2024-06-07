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
  <script src="http://cdn.tailwindcss.com"></script>
  <title>DIOP TRANSIT</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/> 
    <style>
        #carte-container {
            width: 100%;
            height: 400px;
            display: none; /* Caché initialement */
        }
    </style>
</head>
<body class="flex h-screen bg-gray-400">
  <div class="h-full w-80 bg-white">
    <h1 class="text-amber-950 flex items-center justify-center">DIOP TRANSIT</h1>
    <img class="rounded-full" src="download (1).jpeg" alt="">
    <div class="text-white h-10 w-30 m-2 hover:scale-110 rounded-lg flex items-center justify-center bg-amber-950"><a href="Cargaison.php">Cargaison</a></div>
    <div class="text-white h-10 w-30 m-2 hover:scale-110 rounded-lg flex items-center justify-center bg-amber-950"><a href="produit.php">Details</a></div>

  </div>
  <div class="w-full h-full flex-col p-4">
  <div class="rounded-lg mb-4 p-4 bg-white">
  <div class="bg-gray-200 rounded-lg p-2 mb-4">
    <input type="text" class="w-full h-8 rounded-lg p-1" placeholder="Rechercher...">
  </div>
  <h2 class="text-lg font-semibold mb-2">Informations des Cargaisons</h2>

 </div>

    <div class="rounded-lg bg-gray-400 p-4 flex overflow-auto">
      <div class="bg-white p-2 rounded-lg mb-4">
  
        <table id="cargo-table" class="min-w-full leading-normal ">
        <thead>
            <tr class="text-left text-white bg-amber-950">
                <th class="px-4 py-2">Numéro</th>
                <th class="px-4 py-2">Type </th>
                <th class="px-4 py-2">Poids</th>
                <th class="px-4 py-2">Date de depart</th>
                <th class="px-4 py-2">Date d'arrivée</th>
                <th class="px-4 py-2">Produits</th>
                <th class="px-4 py-2">Distance</th>
                <th class="px-4 py-2">+Produits</th>
                <th class="px-10 py-2">Action</th>
            </tr>
        </thead>
        <tbody id="cargo-table-body">
    <?php foreach ($cargaisons as $cargaison): ?>
        <tr>
            <td class="px-4 py-2"><?= htmlspecialchars($cargaison['number']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($cargaison['type']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($cargaison['weight']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($cargaison['dateDepart']) ?></td> 
            <td class="px-4 py-2"><?= htmlspecialchars($cargaison['dateArrivee']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($cargaison['products']) ?></td>
            <td class="px-4 py-2"><?= number_format($cargaison['distance'], 2)?> Km</td>
            <td class="px-4 py-2 p-2 m-2">
            <button class="ajout_produits text-white rounded-lg bg-gray-400" data-id=<?=$cargaison['number']?> >+Produits</button>
            </td>
            <td class="px-4 py-2">
                <button class="ajout_produits text-white rounded-lg bg-amber-950" id=<?=$cargaison['number']?> ><?=$cargaison['etat']?></button>
                <button class="ajout_produits text-white rounded-lg bg-green-800" id=<?=$cargaison['number']?> ><?=$cargaison['progres']?></button>
            </td>
        </tr>
       
    <?php   endforeach; ?>
</tbody>

<!-- Modal -->
<div id="productFormModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 shadow-md">
    <div class="bg-amber-950 text-white rounded-lg shadow-lg p-6 max-w-md w-full">
        <h2 class="mb-4 text-xl font-bold">Ajouter un Produit</h2>
        <form action="ajouter_produit.php" method="POST"  id="productForm">
            <div class="mb-4 flex flex-wrap">
                <div class="w-full md:w-1/2 md:pr-2">
                    <label class="block text-white text-sm font-bold mb-2" for="firstName">Prénom</label>
                    <input name="firstName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="firstName" type="text" placeholder="Prénom">
                    <div class="text-red-500 text-xs italic hidden" id="firstNameError">Veuillez entrer votre prénom.</div>
                </div>
                <div class="w-full md:w-1/2 md:pl-2">
                    <label class="block text-white text-sm font-bold mb-2" for="lastName">Nom</label>
                    <input name="lastName" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="lastName" type="text" placeholder="Nom">
                    <div class="text-red-500 text-xs italic hidden" id="lastNameError">Veuillez entrer votre nom.</div>
                </div>
                <div class="w-40 m-2 mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="phoneNumber">Téléphone</label>
                    <input name="phoneNumber" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="phoneNumber" type="tel" placeholder="Téléphone">
                    <div class="text-red-500 text-xs italic hidden" id="phoneNumberError">Veuillez entrer votre numéro de téléphone.</div>
                </div>
                <div class="w-40 m-2 mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="address">Adresse</label>
                    <input name="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="address" type="text" placeholder="Adresse">
                    <div class="text-red-500 text-xs italic hidden" id="addressError">Veuillez entrer votre adresse.</div>
                </div>
                <div class="w-40 m-2 mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="email">Email (facultatif)</label>
                    <input name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email">
                    <div class="text-red-500 text-xs italic hidden" id="emailError">Veuillez entrer une adresse email valide.</div>
                </div>
                <div class="w-40 m-2 mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="productNumber">Nombre de produits</label>
                    <input name="productNumber" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="productNumber" type="number" placeholder="Nombre de produits">
                    <div class="text-red-500 text-xs italic hidden" id="productNumberError">Veuillez entrer le nombre de produits.</div>
                </div>
                <div class="w-40 m-2 mb-4">
                    <label class="block text-white text-sm font-bold mb-2" for="weight">Poids</label>
                    <input name="weight" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="weight" type="number" placeholder="Poids">
                    <div class="text-red-500 text-xs italic hidden" id="weightError">Veuillez entrer le poids.</div>
                </div>
                  <div class="w-40 m-2 mb-4">
                       <label class="block text-white text-sm font-bold mb-2" for="productType">Type de produit</label>
                        <select name="productType" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="productType">
                            <option value="" disabled selected>Type de produit</option>
                            <option value="incassable">Incassable</option>
                            <option value="chimique">Chimique</option>
                            <option value="materiel">Matériel</option>
                       </select>
                     <div class="text-red-500 text-xs italic hidden" id="productTypeError">Veuillez entrer le Type de produit.</div>
                   </div>
 
                    <div class="w-40 m-2 mb-4">
                        <label class="block text-white text-sm font-bold mb-2" for="cargoType">Type de cargaison</label>
                        <select name="cargoType" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="cargoType">
                            <option value="" disabled selected>Type de cargaison</option>
                            <option value="maritime">Maritime</option>
                            <option value="terrestre">Terrestre</option>
                            <option value="aerienne">Aérienne</option>
                        </select>
                        <div class="text-red-500 text-xs italic hidden" id="cargoTypeError">Veuillez entrer le type de cargaison.</div>
                    </div>

            </div>
            <div class="flex items-center justify-between">
                <button  class="bg-gray-400 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" id="submitButton">Ajouter</button>
                <button type="button" class="bg-gray-400 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" id="closeModalButton">Close</button>
            </div>
        </form>
    </div>
</div>

     </table>
    </div>
      <div class="bg-amber-950 absolute p-4 m-2 ml-[80rem] rounded-lg">
        <h1 class="text-white text-2xl flex items-center justify-center mb-4">Ajouter une cargaison</h1>
    <form id="cargaisonForm" action="a" class="space-y-4">
        <select id="type_cargaison" class="block w-full h-10 rounded-lg p-2">
            <option value="" disabled selected>Type de cargaison</option>
            <option value="Maritime">Maritime</option>
            <option value="Aerienne">Aérienne</option>
            <option value="Terrestre">Terrestre</option>
        </select>
        <div id="error-type" class="text-red-500 mt-2" style="display: none;">Veuillez saisir un type de cargaison</div>
        <input id="date-depart" type="date" class="block w-full h-10 rounded-lg p-2" placeholder="Date de départ">
        <div id="error-date-depart" class="text-red-500 mt-2" style="display: none;">Veuillez saisir une date de depart</div>
        <input id="date-arrivee" type="date" class="block w-full h-10 rounded-lg p-2" placeholder="Date d'arrivée">
        <div id="error-date-arrivee" class="text-red-500 mt-2" style="display: none;">Veuillez saisir une date d'arrivée</div>
        <div class="flex items-center space-x-4">
            <label class="flex items-center text-white">
                <input type="checkbox" id="check-nbre-produits" class="mr-2"> Nombre de Produits
            </label>
            <label class="flex items-center text-white">
                <input type="checkbox" id="check-poids" class="mr-2"> Poids total
            </label>
        </div>

        <input id="nbre-produits" type="number" class="block w-full h-10 rounded-lg p-2" placeholder="Nombre de Produits" disabled>
        <div id="error-produits" class="text-red-500 mt-2" style="display: none;">Veuillez saisir le nombre de produits</div>
        <input id="poids" type="number" class="block w-full h-10 rounded-lg p-2" placeholder="Poids total" disabled>
        <div id="error-poids" class="text-red-500 mt-2" style="display: none;">Veuillez saisir le poids total</div>
        <button type="button" id="carte-button" class="block w-full h-10 bg-white rounded-lg text-amber-950">Choisir le Trajet sur la Carte</button>
        <div id="carte-container" class="w-full h-64 bg-gray-200 rounded-lg mt-4"></div>
        <div id="error-message" class="text-red-500 mt-2" style="display: none;">Le trajet est obligatoire</div>
        <button type="submit" class="block w-full h-10 bg-gray-400 rounded-lg">Validé</button>
    </form>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  

     
      </div>
    </div>
  </div>
  <script src="../dist/code.js"></script>
<script>
  // Fonction de recherche
function searchInTable(inputValue) {
    const rows = document.querySelectorAll('#cargo-table-body tr');
    rows.forEach(row => {
        const cargoNumberCell = row.querySelector('td:first-child');
        const cargoTypeCell = row.querySelector('td:nth-child(2)');
        const cargoNumber = cargoNumberCell.textContent.trim();
        const cargoType = cargoTypeCell.textContent.trim().toLowerCase();

        // Vérifier si le numéro de cargaison correspond à la valeur saisie
        const matchCargoNumber = cargoNumber.includes(inputValue);

        // Vérifier si le type de cargaison correspond à la valeur saisie (au moins trois lettres)
        const matchCargoType = cargoType.startsWith(inputValue.toLowerCase());

        if (matchCargoNumber || matchCargoType) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Écouteur d'événement pour le champ de recherche
const searchInput = document.querySelector('.bg-gray-200 input');
searchInput.addEventListener('input', () => {
    const inputValue = searchInput.value.trim();
    searchInTable(inputValue);
});
</script>
<script>
 /*  const fs = require('fs');

// Définir le nom du fichier JSON
const fileName = 'basedonnee.json';

// Fonction pour lire les données depuis le fichier JSON
function loadDataFromFile() {
    try {
        const jsonData = fs.readFileSync(fileName, 'utf8');
        const cargoData = JSON.parse(jsonData);
        return cargoData.cargaisons || [];
    } catch (err) {
        // En cas d'erreur de lecture ou de fichier inexistant, retourner un tableau vide
        console.error('Error reading file:', err);
        return [];
    }
}

// Fonction pour sauvegarder les données dans le fichier JSON
function saveDataToFile(data) {
    const cargoData = { cargaisons: data };
    const jsonData = JSON.stringify(cargoData);
    fs.writeFileSync(fileName, jsonData);
}

// Récupérer les données actuelles du fichier JSON
let cargaison = loadDataFromFile();

// Ajouter les nouvelles données au tableau
const newCargo = {
    number: cargoTableBody.children.length + 1,
    type: type,
    weight: weight !== null ? weight : '---',
    product: nbreProduits !== null ? nbreProduits : '---',
    dateDepart: dateDepart,
    dateArrivee: dateArrivee,
    distance: distance !== null ? distance.toFixed(2) + ' km' : '---'
};
cargaison.push(newCargo);

// Sauvegarder les données mises à jour dans le fichier JSON
saveDataToFile(cargaison);
 */
</script>
</body>
</html>
