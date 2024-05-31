"use strict";
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
let cargaison = [];
document.addEventListener('DOMContentLoaded', function () {
    const cargoForm = document.getElementById('cargaisonForm');
    const carteButton = document.getElementById('carte-button');
    const carteContainer = document.getElementById('carte-container');
    const checkNbreProduits = document.getElementById('check-nbre-produits');
    const checkPoids = document.getElementById('check-poids');
    const poidsInput = document.getElementById('poids');
    const nbreProduitsInput = document.getElementById('nbre-produits');
    const errorMessage = document.getElementById('error-message');
    let startPoint = null;
    let endPoint = null;
    let distance = null;
    // Fonction pour générer un numéro unique de 4 chiffres
    function generateUniqueNumber() {
        return Math.floor(1000 + Math.random() * 9000);
    }
    // Gestion des cases à cocher
    checkNbreProduits.addEventListener('change', function () {
        if (checkNbreProduits.checked) {
            checkPoids.checked = false;
            nbreProduitsInput.disabled = false;
            poidsInput.disabled = true;
            poidsInput.value = '';
        }
        else {
            nbreProduitsInput.disabled = true;
            nbreProduitsInput.value = '';
        }
    });
    checkPoids.addEventListener('change', function () {
        if (checkPoids.checked) {
            checkNbreProduits.checked = false;
            poidsInput.disabled = false;
            nbreProduitsInput.disabled = true;
            nbreProduitsInput.value = '';
        }
        else {
            poidsInput.disabled = true;
            poidsInput.value = '';
        }
    });
    carteButton.addEventListener('click', function () {
        carteContainer.style.display = 'block';
        const map = L.map('carte-container').setView([51.505, -0.09], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        const startMarker = L.marker([51.5, -0.09], { draggable: true }).addTo(map)
            .bindPopup('Point de départ. Déplacez-moi!')
            .openPopup();
        const endMarker = L.marker([51.51, -0.1], { draggable: true }).addTo(map)
            .bindPopup('Point d\'arrivée. Déplacez-moi!')
            .openPopup();
        startMarker.on('move', function (e) {
            startPoint = e.latlng;
            calculateDistance();
        });
        endMarker.on('move', function (e) {
            endPoint = e.latlng;
            calculateDistance();
        });
        function calculateDistance() {
            if (startPoint && endPoint) {
                distance = startPoint.distanceTo(endPoint) / 1000;
                console.log('Distance: ', distance, 'km');
            }
        }
    });
    cargoForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const type = document.getElementById('type_cargaison').value;
        const dateDepart = document.getElementById('date-depart').value;
        const dateArrivee = document.getElementById('date-arrivee').value;
        const weight = poidsInput.disabled ? null : parseFloat(poidsInput.value);
        const nbreProduits = nbreProduitsInput.disabled ? null : parseFloat(nbreProduitsInput.value);
        let isValid = true;
        const currentDate = new Date().getTime();
        const dateDepartTime = new Date(dateDepart).getTime();
        const dateArriveeTime = new Date(dateArrivee).getTime();
        if (!type) {
            document.getElementById('error-type').style.display = 'block';
            isValid = false;
        }
        else {
            document.getElementById('error-type').style.display = 'none';
        }
        if (!dateDepart) {
            document.getElementById('error-date-depart').style.display = 'block';
            isValid = false;
        }
        else if (dateDepartTime < currentDate) {
            document.getElementById('error-date-depart').textContent = 'La date de départ ne peut pas être antérieure à la date du jour.';
            document.getElementById('error-date-depart').style.display = 'block';
            isValid = false;
        }
        else {
            document.getElementById('error-date-depart').style.display = 'none';
        }
        if (!dateArrivee) {
            document.getElementById('error-date-arrivee').style.display = 'block';
            isValid = false;
        }
        else if (dateArriveeTime < dateDepartTime) {
            document.getElementById('error-date-arrivee').textContent = 'La date d\'arrivée ne peut pas être antérieure à la date de départ.';
            document.getElementById('error-date-arrivee').style.display = 'block';
            isValid = false;
        }
        else {
            document.getElementById('error-date-arrivee').style.display = 'none';
        }
        if (weight === null && nbreProduits === null) {
            document.getElementById('error-produits').style.display = 'block';
            document.getElementById('error-poids').style.display = 'block';
            isValid = false;
        }
        else {
            document.getElementById('error-produits').style.display = 'none';
            document.getElementById('error-poids').style.display = 'none';
        }
        if (isValid && startPoint && endPoint && distance !== null) {
            errorMessage.style.display = 'none';
            const newCargo = {
                number: generateUniqueNumber(),
                type: type,
                weight: weight !== null ? weight : 'N/A',
                product: nbreProduits !== null ? nbreProduits : 'N/A',
                dateDepart: dateDepart,
                dateArrivee: dateArrivee,
                distance: distance,
                produit: []
            };
            // Fonction pour envoyer les données à savejson.php
            function saveCargoData(cargo) {
                return __awaiter(this, void 0, void 0, function* () {
                    try {
                        const response = yield fetch('savejson.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(cargo),
                        });
                        if (response.ok) {
                            console.log('Données sauvegardées avec succès');
                        }
                        else {
                            console.error('Erreur lors de la sauvegarde des données', response.statusText);
                        }
                    }
                    catch (error) {
                        console.error('Erreur réseau ou autre', error);
                    }
                });
            }
            // Appel de la fonction pour sauvegarder le tableau Cargo
            saveCargoData(newCargo);
            console.log(newCargo);
            const cargoTableBody = document.getElementById('cargo-table-body');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
      <td class="px-4 py-2">${newCargo.number}</td>
      <td class="px-4 py-2">${newCargo.type}</td>
      <td class="px-4 py-2">${newCargo.weight !== 'N/A' ? newCargo.weight : '---'}</td>
      <td class="px-4 py-2">${newCargo.dateDepart}</td>
      <td class="px-4 py-2">${newCargo.dateArrivee}</td>
      <td class="px-4 py-2">${newCargo.product !== 'N/A' ? newCargo.product : '---'}</td>
      <td class="px-4 py-2">${newCargo.distance !== 'N/A' ? newCargo.distance.toFixed(2) : '---'} km</td>
 
    `;
            cargoTableBody.appendChild(newRow);
            cargoForm.reset();
            // Réinitialiser la distance pour éviter les mises à jour continues
            distance = null;
        }
        else {
            errorMessage.style.display = 'block';
        }
    });
});
