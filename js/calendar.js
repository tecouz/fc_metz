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

  // Sélectionner les éléments HTML pour la table du calendrier et le conteneur du formulaire d'ajout d'événement
  const calendar = document.getElementById("calendar");
  const eventDetails = document.getElementById("eventDetails");
  const eventList = document.getElementById("eventList");

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

  // Sélectionner les cellules du calendrier
  const calendarCells = document.querySelectorAll("#calendar td");

  // Ajouter un gestionnaire d'événement pour les clics sur les cellules
  calendarCells.forEach((cell) => {
    cell.addEventListener("click", () => {
      const selectedDate = new Date(
        yearSelect.value,
        monthSelect.value - 1,
        cell.textContent
      );
      const formattedDate = selectedDate.toISOString().split("T")[0];

      // Afficher le formulaire d'ajout d'événement
      eventDetails.style.display = "block";

      // Remplir le champ de date avec la date sélectionnée
      const eventDateInput = document.getElementById("eventDate");
      eventDateInput.value = formattedDate;

      // Récupérer les événements pour la date sélectionnée
      fetchEvents(formattedDate);
    });
  });

  // Gestionnaire d'événement pour le bouton "Ajouter"
  const addEventButton = document.getElementById("addEvent");
  addEventButton.addEventListener("click", () => {
    const eventTitle = document.getElementById("eventTitle").value;
    const eventDate = document.getElementById("eventDate").value;

    // Ajouter un nouvel événement
    addEvent(eventTitle, eventDate);

    // Réinitialiser le formulaire
    eventForm.reset();
    eventDetails.style.display = "none";
    eventList.innerHTML = ""; // Effacer la liste des événements
  });

  // Gestionnaire d'événement pour le bouton "Annuler"
  const cancelEventButton = document.getElementById("cancelEvent");
  cancelEventButton.addEventListener("click", () => {
    eventDetails.style.display = "none";
    eventList.innerHTML = ""; // Effacer la liste des événements
  });
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

  // Sélectionner les cellules du calendrier après la génération
  const calendarCells = document.querySelectorAll("#calendar td");

  // Ajouter un gestionnaire d'événement pour les clics sur les cellules
  calendarCells.forEach((cell) => {
    cell.addEventListener("click", () => {
      const selectedDate = new Date(
        yearSelect.value,
        monthSelect.value - 1,
        cell.textContent
      );
      const formattedDate = selectedDate.toISOString().split("T")[0];

      // Afficher le formulaire d'ajout d'événement
      eventDetails.style.display = "block";

      // Remplir le champ de date avec la date sélectionnée
      const eventDateInput = document.getElementById("eventDate");
      eventDateInput.value = formattedDate;

      // Récupérer les événements pour la date sélectionnée
      fetchEvents(formattedDate);
    });
  });
}

function addEvent(title, eventDate) {
  // Créer un objet FormData pour stocker les données de l'événement
  const formData = new FormData();
  formData.append("action", "add_event");
  formData.append("eventTitle", title);
  formData.append("eventDate", eventDate);

  // Récupérer les autres données du formulaire
  const eventDescription = document.getElementById("eventDescription").value;
  const eventStartTime = document.getElementById("eventStartTime").value;
  const eventEndTime = document.getElementById("eventEndTime").value;
  const eventLocation = document.getElementById("eventLocation").value;
  const eventParticipants = document.getElementById("eventParticipants").value;

  // Ajouter les autres données au FormData
  formData.append("eventDescription", eventDescription);
  formData.append("eventStartTime", eventStartTime);
  formData.append("eventEndTime", eventEndTime);
  formData.append("eventLocation", eventLocation);
  formData.append("eventParticipants", eventParticipants);

  // Envoyer la requête AJAX
  fetch("add_event.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.text())
    .then((data) => {
      console.log(data); // Afficher la réponse du serveur dans la console
      // Récupérer les événements pour la date sélectionnée
      fetchEvents(eventDate);
    })
    .catch((error) => {
      console.error("Erreur lors de l'ajout de l'événement :", error);
    });
}

function fetchEvents(eventDate) {
  // Envoyer une requête AJAX pour récupérer les événements pour la date sélectionnée
  fetch(`get_events.php?date=${eventDate}`)
    .then((response) => response.json())
    .then((events) => {
      const eventList = document.getElementById("eventList");
      eventList.innerHTML = ""; // Effacer la liste des événements

      // Afficher les événements dans la liste
      if (events.length > 0) {
        const ul = document.createElement("ul");
        events.forEach((event) => {
          const li = document.createElement("li");
          li.textContent = `${event.title} (${event.start_time} - ${event.end_time})`;
          ul.appendChild(li);
        });
        eventList.appendChild(ul);
      } else {
        eventList.textContent = "Aucun événement pour cette date.";
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des événements :", error);
    });
}
s;
