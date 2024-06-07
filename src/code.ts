interface Cargo {
  number: any;
  type: string;
  weight: number | 'N/A';
  products: number | 'N/A';
  dateDepart: string;
  dateArrivee: string;
  distance: number | 'N/A';
  etat: "ouvert"|"fermer";
  progres: "En attente"|"En cours"|"arrivee";
  produit: Product[];

}
interface Product {
  firstName: string;
  lastName: string;
  phoneNumber: string;
  address: string;
  email?: string;
  productNumber: number;
  weight: number;
  productType: string;
  cargoType: string;
  code: string;
}
let cargoData: Cargo[] = [];


function generateRandomCode(): string {
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const digits = '0123456789';
  let code = '';
  for (let i = 0; i < 2; i++) {
      code += characters.charAt(Math.floor(Math.random() * characters.length));
  }
  for (let i = 0; i < 2; i++) {
      code += digits.charAt(Math.floor(Math.random() * digits.length));
  }
  return code;
}
let produits = [];

let cargaison: Cargo[] = [];

document.addEventListener('DOMContentLoaded', function () {
  const cargoForm = document.getElementById('cargaisonForm') as HTMLFormElement;
  const carteButton = document.getElementById('carte-button') as HTMLButtonElement;
  const carteContainer = document.getElementById('carte-container') as HTMLDivElement;
  const checkNbreProduits = document.getElementById('check-nbre-produits') as HTMLInputElement;
  const checkPoids = document.getElementById('check-poids') as HTMLInputElement;
  const poidsInput = document.getElementById('poids') as HTMLInputElement;
  const nbreProduitsInput = document.getElementById('nbre-produits') as HTMLInputElement;
  const errorMessage = document.getElementById('error-message') as HTMLElement;
  let startPoint: L.LatLng | null = null;
  let endPoint: L.LatLng | null = null;
  let distance: number | null = null;

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
    } else {
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
    } else {
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

    startMarker.on('move', function (e: L.LeafletEvent) {
      startPoint = (e as L.LeafletMouseEvent).latlng;
      calculateDistance();
    });

    endMarker.on('move', function (e: any) {
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

  cargoForm.addEventListener('submit', (e: Event) => {
    e.preventDefault();

    const type = (document.getElementById('type_cargaison') as HTMLSelectElement).value;
    const dateDepart = (document.getElementById('date-depart') as HTMLInputElement).value;
    const dateArrivee = (document.getElementById('date-arrivee') as HTMLInputElement).value;
    const weight = poidsInput.disabled ? null : parseFloat(poidsInput.value);
    const nbreProduits = nbreProduitsInput.disabled ? null : parseFloat(nbreProduitsInput.value);

    let isValid = true;
    const currentDate = new Date().getTime();
    const dateDepartTime = new Date(dateDepart).getTime();
    const dateArriveeTime = new Date(dateArrivee).getTime();


    if (!type) {
      document.getElementById('error-type')!.style.display = 'block';
      isValid = false;
    } else {
      document.getElementById('error-type')!.style.display = 'none';
    }

    if (!dateDepart) {
      document.getElementById('error-date-depart')!.style.display = 'block';
      isValid = false;
    } else if (dateDepartTime < currentDate) {
      document.getElementById('error-date-depart')!.textContent = 'La date de départ ne peut pas être antérieure à la date du jour.';
      document.getElementById('error-date-depart')!.style.display = 'block';
      isValid = false;
    } else {
      document.getElementById('error-date-depart')!.style.display = 'none';
    }

    if (!dateArrivee) {
      document.getElementById('error-date-arrivee')!.style.display = 'block';
      isValid = false;
    } else if (dateArriveeTime < dateDepartTime) {
      document.getElementById('error-date-arrivee')!.textContent = 'La date d\'arrivée ne peut pas être antérieure à la date de départ.';
      document.getElementById('error-date-arrivee')!.style.display = 'block';
      isValid = false;
    } else {
      document.getElementById('error-date-arrivee')!.style.display = 'none';
    }

    if (weight === null && nbreProduits === null) {
      document.getElementById('error-produits')!.style.display = 'block';
      document.getElementById('error-poids')!.style.display = 'block';
      isValid = false;
    } else {
      document.getElementById('error-produits')!.style.display = 'none';
      document.getElementById('error-poids')!.style.display = 'none';
    }

    if (isValid && startPoint && endPoint && distance !== null) {
      errorMessage.style.display = 'none';




      
      const newCargo: Cargo = {
        number: generateUniqueNumber(),
        type: type,
        weight: weight !== null ? weight : 'N/A',
        dateDepart: dateDepart,
        dateArrivee: dateArrivee,
        products: nbreProduits !== null ? nbreProduits : 'N/A',
        distance: distance,
        etat: "ouvert",
        progres:"En attente",
        produit: []
      };

     
      // Fonction pour envoyer les données à savejson.php
      async function saveCargoData(cargo: Cargo) {
        try {
          const response = await fetch('savejson.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(cargo),
          });

          if (response.ok) {
            console.log('Données sauvegardées avec succès');
          } else {
            console.error('Erreur lors de la sauvegarde des données', response.statusText);
          }
        } catch (error) {
          console.error('Erreur réseau ou autre', error);
        }
      }

      // Appel de la fonction pour sauvegarder le tableau Cargo
      saveCargoData(newCargo);
      console.log(newCargo);
 
     
    const cargoTableBody = document.getElementById('cargo-table-body') as HTMLElement;
      const newRow = document.createElement('tr');
      newRow.innerHTML = `
      <td class="px-4 py-2">${newCargo.number}</td>
      <td class="px-4 py-2">${newCargo.type}</td>
      <td class="px-4 py-2">${newCargo.weight !== 'N/A' ? newCargo.weight : '---'}</td>
      <td class="px-4 py-2">${newCargo.dateDepart}</td>
      <td class="px-4 py-2">${newCargo.dateArrivee}</td>
      <td class="px-4 py-2">${newCargo.products !== 'N/A' ? newCargo.products : '---'}</td>
      <td class="px-4 py-2">${newCargo.distance !== 'N/A' ? newCargo.distance.toFixed(2) : '---'} km</td>
      <td class="px-4 py-2 p-2 m-2">
      <button class="ajout_produits text-white rounded-lg bg-gray-400" data-id=${newCargo.number}>+Produits</button>
      </td>
      <td class="px-4 py-2">
      <button class="text-white rounded-lg bg-amber-950" id=${newCargo.number}>${newCargo.etat}</button>
      <button class="text-white rounded-lg bg-green-800" id=${newCargo.number}>${newCargo.progres}</button>

      </td>
    
    `;
    
      cargoTableBody.appendChild(newRow);
      cargoForm.reset();

      // Réinitialiser la distance pour éviter les mises à jour continues
      distance = null;

    } else {
      errorMessage.style.display = 'block';
    }
  });

});
document.addEventListener('DOMContentLoaded', () => {
  const productForm = document.getElementById('productForm') as HTMLFormElement;
  const modal = document.getElementById('productFormModal') as HTMLDivElement;
  const closeModalButton = document.getElementById('closeModalButton') as HTMLButtonElement;
  let currentCargoId: number | null = null;

  const buttons = document.querySelectorAll('.ajout_produits');
  buttons.forEach(button => {
    button.addEventListener('click', (event) => {
      const target = event.target as HTMLElement;
      const id = target.id;
      changeEtat(id);

       /* changeEtat(id); */
      const dataId = target.getAttribute('data-id');
      if (dataId !== null) {
        currentCargoId = parseInt(dataId, 10);
        console.log('ID du cargaison récupéré:', currentCargoId);
        if (modal) {
          modal.classList.remove('hidden');
        }
      }
    });
  });

  if (closeModalButton && modal) {
    closeModalButton.addEventListener('click', () => {
      modal.classList.add('hidden');
    });

    window.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        modal.classList.add('hidden');
      }
    });

    modal.addEventListener('click', (event) => {
      if (event.target === modal) {
        modal.classList.add('hidden');
      }
    });
  }

  productForm.addEventListener('submit', async (event: Event) => {
    event.preventDefault();

    const firstName = (document.getElementById('firstName') as HTMLInputElement).value;
    const lastName = (document.getElementById('lastName') as HTMLInputElement).value;
    const phoneNumber = (document.getElementById('phoneNumber') as HTMLInputElement).value;
    const address = (document.getElementById('address') as HTMLInputElement).value;
    const email = (document.getElementById('email') as HTMLInputElement).value;
    const productNumber = (document.getElementById('productNumber') as HTMLInputElement).value;
    const weight = (document.getElementById('weight') as HTMLInputElement).value;
    const productType = (document.getElementById('productType') as HTMLInputElement).value;
    const cargoType = (document.getElementById('cargoType') as HTMLInputElement).value;

    let isValid = true;

    isValid = validateField(firstName, 'firstNameError') && isValid;
    isValid = validateField(lastName, 'lastNameError') && isValid;
    isValid = validateField(phoneNumber, 'phoneNumberError') && isValid;
    isValid = validateField(address, 'addressError') && isValid;
    isValid = validateOptionalField(email, 'emailError') && isValid;
    isValid = validateField(productNumber, 'productNumberError') && isValid;
    isValid = validateField(weight, 'weightError') && isValid;
    isValid = validateField(productType, 'productTypeError') && isValid;
    isValid = validateField(cargoType, 'cargoTypeError') && isValid;

    if (isValid && currentCargoId !== null) {
      const newProduct: Product = {
        firstName,
        lastName,
        phoneNumber,
        address,
        email,
        productNumber: parseInt(productNumber, 10),
        weight: parseFloat(weight),
        productType,
        cargoType,
        code: generateRandomCode()
      };

      try {
        // Fetch existing data from garde.json
        const data = await getDataFromJsonFile();

        // Find the cargo by currentCargoId
        const cargo = data.find((item: any) => item.number === currentCargoId);
        console.log(cargo);
        if (cargo) {

          // Push the new product into the produit array
          cargo.produit.push(newProduct);
          console.log(currentCargoId);
          
         
          // Save the updated data back to the garde.json file
          await saveDataToJsonFile(newProduct, `?ajouter=${currentCargoId}`);

          console.log('Produit sauvegardé avec succès');
        } else {
          console.error('Cargaison non trouvée');
        }
      } catch (error) {
        console.error('Erreur lors de la sauvegarde du produit', error);
      }

      // Réinitialiser le formulaire
      productForm.reset();
      if (modal) {
        modal.classList.add('hidden');
      }

      console.log('Produit ajouté:', newProduct);
    }
  });

  function validateField(value: string, errorId: string): boolean {
    const errorElement = document.getElementById(errorId) as HTMLElement;
    if (!value.trim()) {
      errorElement.style.display = 'block';
      return false;
    } else {
      errorElement.style.display = 'none';
      return true;
    }
  }

  function validateOptionalField(value: string, errorId: string): boolean {
    const errorElement = document.getElementById(errorId) as HTMLElement;
    if (value.trim() && !validateEmail(value)) {
      errorElement.style.display = 'block';
      return false;
    } else {
      errorElement.style.display = 'none';
      return true;
    }
  }

  function validateEmail(email: string): boolean {
    // Expression régulière pour la validation de l'email
    const re = /\S+@\S+\.\S+/;
    return re.test(email);
  }

  async function getDataFromJsonFile(): Promise<any> {
    const response = await fetch('garde.json');
    if (!response.ok) {
      throw new Error(`HTTP error! Status: ${response.status}`);
    }
    const data = await response.json();
    return data;
  } 


  async function saveDataToJsonFile(data: any, test:string=""): Promise<void> {
    let url = 'savejson.php';
    if(test){ url+=test;}
    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.statusText}`);
      }
    } catch (error) {
      console.error('Erreur lors de la sauvegarde des données', error);
    }
  }
});

async function getDataFromJsonFile2(): Promise<any> {
  const response = await fetch('garde.json');
  if (!response.ok) {
    throw new Error(`HTTP error! Status: ${response.status}`);
  }
  const data = await response.json();
  return data;
} 


 async function changeEtat(id: string): Promise<void> {
  try {
     const cargaison = await getDataFromJsonFile2(); 
     const data = cargaison.find((item: any) => item.number === parseInt(id, 10));
   /*   console.log(data); */
   if (data) {

     if(data.etat === "ouvert"){
       data.etat = "fermer";
       data.textContent= "fermer";
       /* updateCargaisons(data); */
       console.log(data.etat);
     }else
     {
       data.etat = "ouvert";
       data.textContent= "ouvert";
       /* updateCargaisons(data); */
       console.log(data.etat);
     }
   }
  } catch (error) {
    console.log(error);

    
  }
} 

/* import fs from 'fs';

async function updateCargaisons(cargaison: any): Promise<void> {
  try {
    const cargaisonsFilePath = 'garde.json'; // Mettez ici le chemin vers votre fichier JSON

    // Lire le contenu du fichier JSON
    const cargaisonsData = JSON.parse(fs.readFileSync(cargaisonsFilePath, 'utf-8'));

    // Trouver l'index de l'élément à mettre à jour dans le tableau
    const index = cargaisonsData.findIndex((item: any) => item.id === cargaison.id);

    if (index === -1) {
      throw new Error("Cargaison not found");
    }

    // Mettre à jour l'élément dans le tableau
    cargaisonsData[index] = cargaison;

    // Écrire le contenu mis à jour dans le fichier JSON
    fs.writeFileSync(cargaisonsFilePath, JSON.stringify(cargaisonsData));

    console.log("Cargaison mise à jour avec succès !");
  } catch (error) {
    console.error("Erreur lors de la mise à jour de la cargaison :", error);
  }
} */