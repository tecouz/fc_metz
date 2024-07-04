document.addEventListener("DOMContentLoaded", function () {
  // Attendre que le DOM soit complètement chargé avant d'exécuter le code

  const monthSelect = document.getElementById("monthSelect");
  const yearSelect = document.getElementById("yearSelect");
  // Récupérer les éléments select pour le mois et l'année

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
  // Tableau contenant les noms des mois

  for (let i = 0; i < months.length; i++) {
    const option = document.createElement("option");
    option.value = i + 1;
    option.text = months[i];
    monthSelect.add(option);
  }
  // Boucle pour ajouter les options de mois dans le select

  const today = new Date();
  const currentMonth = today.getMonth() + 1;
  const currentYear = today.getFullYear();
  // Récupérer le mois et l'année actuels

  for (let year = currentYear - 5; year <= currentYear + 5; year++) {
    const option = document.createElement("option");
    option.value = year;
    option.text = year;
    yearSelect.add(option);
  }
  // Boucle pour ajouter les options d'années dans le select (5 ans avant et après l'année actuelle)

  monthSelect.value = currentMonth;
  yearSelect.value = currentYear;
  // Définir les valeurs par défaut des selects avec le mois et l'année actuels

  monthSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
    fetchEvents(monthSelect.value, yearSelect.value);
  });
  // Écouter les changements sur le select de mois et générer le calendrier et récupérer les événements

  yearSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
    fetchEvents(monthSelect.value, yearSelect.value);
  });
  // Écouter les changements sur le select d'année et générer le calendrier et récupérer les événements

  generateCalendar(currentMonth, currentYear);
  fetchEvents(currentMonth, currentYear);
  // Générer le calendrier et récupérer les événements pour le mois et l'année actuels
});

function generateCalendar(month, year) {
  const calendar = document.getElementById("calendar");
  calendar.innerHTML = "";
  // Récupérer l'élément pour afficher le calendrier et le vider

  const firstDayOfMonth = new Date(year, month - 1, 1);
  const startingDay = firstDayOfMonth.getDay();
  const daysInMonth = new Date(year, month, 0).getDate();
  // Calculer le premier jour du mois, le jour de la semaine de ce premier jour, et le nombre de jours dans le mois

  const table = document.createElement("table");
  const tbody = document.createElement("tbody");
  // Créer les éléments table et tbody pour afficher le calendrier

  const headerRow = document.createElement("tr");
  const daysOfWeek = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
  for (const day of daysOfWeek) {
    const th = document.createElement("th");
    th.textContent = day;
    headerRow.appendChild(th);
  }
  tbody.appendChild(headerRow);
  // Créer la ligne d'en-tête avec les jours de la semaine

  let date = 1;
  for (let i = 0; i < 6; i++) {
    const row = document.createElement("tr");
    for (let j = 0; j < 7; j++) {
      if (i === 0 && j < startingDay) {
        const cell = document.createElement("td");
        row.appendChild(cell);
      } else if (date > daysInMonth) {
        break;
      } else {
        const cell = document.createElement("td");
        cell.textContent = date;
        row.appendChild(cell);
        date++;
      }
    }
    tbody.appendChild(row);
    if (date > daysInMonth) {
      break;
    }
  }
  // Boucle pour créer les lignes du calendrier avec les jours du mois

  table.appendChild(tbody);
  calendar.appendChild(table);
  // Ajouter le tbody à la table et la table à l'élément du calendrier
}

function fetchEvents(month, year) {
  fetch(`index.php?page=2&month=${month}&year=${year}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.text();
    })
    .then((data) => {
      const eventList = document.getElementById("eventList");
      eventList.innerHTML = data;
    })
    .catch((error) => {
      console.error(
        "There has been a problem with your fetch operation:",
        error
      );
    });
}
// Fonction pour récupérer les événements pour un mois et une année donnés via une requête fetch
