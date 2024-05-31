

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
    <div class="text-white h-10 w-30 m-2 hover:scale-110 rounded-lg flex items-center justify-center bg-amber-950"><a href="">Cargaison</a></div>
    <div class="text-white h-10 w-30 m-2 hover:scale-110 rounded-lg flex items-center justify-center bg-amber-950"><a href="">Produits</a></div>

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
                <th class="px-4 py-2">Nombre de Produits</th>
                <th class="px-4 py-2">Distance</th>
                <th class="px-4 py-2">lieu depart</th>
                <th class="px-4 py-2">Lieu d'arrivée</th>
            
            </tr>
        </thead>
        <tbody id="cargo-table-body">
         <?php 
        if(isset($_POST)) {
            var_dump($_POST);
        }
          ?>
        </tbody>

    </table>
      </div>
      <div class="bg-amber-950 absolute p-4 m-2 ml-[80rem] rounded-lg">
        <h1 class="text-white text-2xl flex items-center justify-center mb-4">Ajouter une cargaison</h1>
    <form id="cargaisonForm" class="space-y-4">
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
  const fs = require('fs');

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

</script>
</body>
</html>
