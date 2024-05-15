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

  // Définir le mois et l'année actuels
  const today = new Date();
  const currentMonth = today.getMonth() + 1; // Les mois dans les objets Date sont indexés à partir de 0
  const currentYear = today.getFullYear();

  // Remplir la liste déroulante des années (par exemple, de 2020 à 2030)
  for (let year = currentYear - 5; year <= currentYear + 5; year++) {
    const option = document.createElement("option");
    option.value = year;
    option.text = year;
    yearSelect.add(option);
  }

  // Sélectionner le mois et l'année actuels dans les listes déroulantes
  monthSelect.value = currentMonth;
  yearSelect.value = currentYear;

  // Gestionnaire d'événement pour la sélection d'un mois
  monthSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
    fetchEvents(monthSelect.value, yearSelect.value);
  });

  // Gestionnaire d'événement pour la sélection d'une année
  yearSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
    fetchEvents(monthSelect.value, yearSelect.value);
  });

  // Génération initiale du calendrier et des événements
  generateCalendar(currentMonth, currentYear);
  fetchEvents(currentMonth, currentYear);

  // Gestionnaire d'événement pour l'ajout d'un événement
  const addEvntBtn = document.getElementById("addEvntBtn");
  if (addEvntBtn) {
    addEvntBtn.addEventListener("click", function () {
      console.log("Clic sur addEvntBtn");
      showAddEventForm();
    });
  } else {
    console.log("La div addEvntBtn n'a pas été trouvée");
  }
});

function fetchEvents(month, year) {
  fetch(`get_events.php?month=${month}&year=${year}`)
    .then((response) => response.json())
    .then((events) => {
      const eventList = document.getElementById("eventList");
      eventList.innerHTML = "";

      if (events.length > 0) {
        const ul = document.createElement("ul");

        events.forEach((event) => {
          const li = document.createElement("li");
          const startDateTime = new Date(
            `${event.event_date} ${event.start_time}`
          );
          const options = {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
          };
          const formattedDate = startDateTime.toLocaleDateString(
            "fr-FR",
            options
          );
          li.textContent = `${event.title} (${formattedDate}, ${event.start_time} - ${event.end_time}) - Participants: ${event.participants}`;

          const editButton = document.createElement("button");
          editButton.textContent = "Modifier";
          editButton.addEventListener("click", () => {});

          const deleteButton = document.createElement("button");
          deleteButton.textContent = "Supprimer";
          deleteButton.addEventListener("click", () => {});

          li.appendChild(editButton);
          li.appendChild(deleteButton);
          ul.appendChild(li);
        });

        eventList.appendChild(ul);
      } else {
        eventList.textContent = "Aucun événement pour ce mois.";
      }
    })
    .catch((error) => {
      console.error("Erreur lors de la récupération des événements :", error);
    });
}

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

function addEvent() {
  // Récupérer les valeurs des champs du formulaire
  const title = document.getElementById("eventTitle").value;
  const date = document.getElementById("eventDate").value;
  const startTime = document.getElementById("eventStartTime").value;
  const endTime = document.getElementById("eventEndTime").value;
  const location = document.getElementById("eventLocation").value;
  const participants = document.getElementById("eventParticipants").value;
  const description = document.getElementById("eventDescription").value;

  // Créer un objet avec les données de l'événement
  const eventData = {
    title,
    date,
    startTime,
    endTime,
    location,
    participants,
    description,
  };

  // Envoyer une requête AJAX pour ajouter l'événement
  fetch("../Scout/add_events.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(eventData),
  })
    .then((response) => {
      // Vérifier si la réponse est réussie (code de statut 200-299)
      if (response.ok) {
        // L'événement a été ajouté avec succès
        alert("L'événement a été ajouté avec succès.");
        // Réinitialiser les champs du formulaire
        document.getElementById("eventForm").reset();
        // Fermer le formulaire d'ajout d'événement
        closeAddEventForm();
        // Rafraîchir la page
        window.location.reload();
      } else {
        // Une erreur s'est produite lors de l'ajout de l'événement
        response.text().then((errorMessage) => {
          alert(
            `Une erreur s'est produite lors de l'ajout de l'événement : ${errorMessage}`
          );
        });
      }
    })
    .catch((error) => {
      console.error("Erreur lors de l'ajout de l'événement :", error);
    });
}

function showAddEventForm(eventData = null) {
  console.log("Fonction showAddEventForm appelée");

  // Créer un formulaire ou une fenêtre modale pour saisir les informations de l'événement
  const eventForm = document.createElement("div");
  eventForm.innerHTML = `
    <h3>Ajouter un événement</h3>
    <form id="eventForm">
      <label for="eventTitle">Titre :</label>
      <input type="text" id="eventTitle" required value="${
        eventData?.title || ""
      }">
      <label for="eventDate">Date :</label>
      <input type="date" id="eventDate" required value="${
        eventData?.date || ""
      }">
      <label for="eventStartTime">Heure de début :</label>
      <input type="time" id="eventStartTime" required value="${
        eventData?.startTime || ""
      }">
      <label for="eventEndTime">Heure de fin :</label>
      <input type="time" id="eventEndTime" required value="${
        eventData?.endTime || ""
      }">
      <label for="eventLocation">Lieu :</label>
      <input type="text" id="eventLocation" required value="${
        eventData?.location || ""
      }">
      <label for="eventParticipants">Participants :</label>
      <input type="text" id="eventParticipants" value="${
        eventData?.participants || ""
      }">
      <label for="eventDescription">Description :</label>
      <textarea id="eventDescription">${eventData?.description || ""}</textarea>
      <button type="button" onclick="addEvent()">Ajouter</button>
      <button type="button" onclick="closeAddEventForm()">Annuler</button>
    </form>
  `;
  document.body.appendChild(eventForm);
}

// Fonction pour fermer le formulaire d'ajout d'événement
function closeAddEventForm() {
  const eventForm = document.querySelector("#eventForm");
  if (eventForm) {
    eventForm.parentNode.removeChild(eventForm);
  }
}
