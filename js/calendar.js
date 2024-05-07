document.addEventListener("DOMContentLoaded", function () {
  // Sélectionner les éléments HTML pour les listes déroulantes des mois et des années
  const monthSelect = document.getElementById("monthSelect");
  const yearSelect = document.getElementById("yearSelect");

  // Remplir la liste déroulante des mois
  const months = [
    "Janvier",
    "Février",
    "Mars",
    "Avril",
    "Mai",
    "Juin",
    "Juillet",
    "Août",
    "Septembre",
    "Octobre",
    "Novembre",
    "Décembre",
  ];
  for (let i = 0; i < months.length; i++) {
    const option = document.createElement("option");
    option.value = i + 1; // Les mois dans les objets Date sont indexés à partir de 0
    option.text = months[i];
    monthSelect.add(option);
  }

  // Remplir la liste déroulante des années (par exemple, de 2020 à 2030)
  const currentYear = new Date().getFullYear();
  for (let year = currentYear - 5; year <= currentYear + 5; year++) {
    const option = document.createElement("option");
    option.value = year;
    option.text = year;
    yearSelect.add(option);
  }

  // Sélectionner l'élément HTML pour le formulaire d'ajout d'événement
  const eventForm = document.getElementById("eventForm");

  // Gestionnaire d'événement pour la soumission du formulaire d'ajout d'événement
  eventForm.addEventListener("submit", (event) => {
    event.preventDefault(); // Empêcher le rechargement de la page

    const eventTitle = document.getElementById("eventTitle").value;
    const eventDate = new Date(document.getElementById("eventDate").value);

    // Ajouter un nouvel événement
    addEvent(eventTitle, eventDate.toISOString().split("T")[0]);

    // Réinitialiser le formulaire
    eventForm.reset();
    eventFormContainer.style.display = "none";
  });

  // Sélectionner les éléments HTML pour la table du calendrier et le conteneur du formulaire d'ajout d'événement
  const calendar = document.getElementById("calendar");
  const eventFormContainer = document.getElementById("eventFormContainer");

  // Gestionnaire d'événement pour la sélection d'un mois
  monthSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
  });

  // Gestionnaire d'événement pour la sélection d'une année
  yearSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
  });

  // Génération initiale du calendrier
  generateCalendar(monthSelect.value, yearSelect.value);
});

function generateCalendar(month, year) {
  const calendar = document.getElementById("calendar");
  calendar.innerHTML = ""; // Vider le contenu précédent

  // Créer un objet Date pour le premier jour du mois spécifié
  const firstDayOfMonth = new Date(year, month - 1, 1);
  // Obtenir le jour de la semaine du premier jour du mois (0 pour dimanche, 1 pour lundi, ..., 6 pour samedi)
  const startingDay = firstDayOfMonth.getDay();

  // Nombre de jours dans le mois spécifié
  const daysInMonth = new Date(year, month, 0).getDate();

  // Créer une table pour afficher le calendrier
  const table = document.createElement("table");
  const tbody = document.createElement("tbody");

  // Créer une ligne pour les en-têtes de jour de la semaine
  const headerRow = document.createElement("tr");
  const daysOfWeek = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
  for (const day of daysOfWeek) {
    const th = document.createElement("th");
    th.textContent = day;
    headerRow.appendChild(th);
  }
  tbody.appendChild(headerRow);

  // Remplir les cases vides avant le premier jour du mois
  let date = 1;
  for (let i = 0; i < 6; i++) {
    const row = document.createElement("tr");
    for (let j = 0; j < 7; j++) {
      if (i === 0 && j < startingDay) {
        // Case vide avant le premier jour du mois
        const cell = document.createElement("td");
        row.appendChild(cell);
      } else if (date > daysInMonth) {
        // Si nous avons dépassé le nombre de jours dans le mois, sortir de la boucle
        break;
      } else {
        // Créer une cellule pour afficher le jour du mois
        const cell = document.createElement("td");
        cell.textContent = date;
        row.appendChild(cell);
        date++;
      }
    }
    tbody.appendChild(row);
    // Si nous avons dépassé le nombre de jours dans le mois, sortir de la boucle
    if (date > daysInMonth) {
      break;
    }
  }

  table.appendChild(tbody);
  calendar.appendChild(table);
}

// Fonction pour ajouter un nouvel événement
function addEvent(title, eventDate) {
  // Implémentez la logique pour ajouter un nouvel événement sur le serveur
}
