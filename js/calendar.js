document.addEventListener("DOMContentLoaded", function () {
  const monthSelect = document.getElementById("monthSelect");
  const yearSelect = document.getElementById("yearSelect");

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
    option.value = i + 1;
    option.text = months[i];
    monthSelect.add(option);
  }

  const today = new Date();
  const currentMonth = today.getMonth() + 1;
  const currentYear = today.getFullYear();

  for (let year = currentYear - 5; year <= currentYear + 5; year++) {
    const option = document.createElement("option");
    option.value = year;
    option.text = year;
    yearSelect.add(option);
  }

  monthSelect.value = currentMonth;
  yearSelect.value = currentYear;

  monthSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
    fetchEvents(monthSelect.value, yearSelect.value);
  });

  yearSelect.addEventListener("change", () => {
    generateCalendar(monthSelect.value, yearSelect.value);
    fetchEvents(monthSelect.value, yearSelect.value);
  });

  generateCalendar(currentMonth, currentYear);
  fetchEvents(currentMonth, currentYear);
});

function generateCalendar(month, year) {
  const calendar = document.getElementById("calendar");
  calendar.innerHTML = "";

  const firstDayOfMonth = new Date(year, month - 1, 1);
  const startingDay = firstDayOfMonth.getDay();
  const daysInMonth = new Date(year, month, 0).getDate();

  const table = document.createElement("table");
  const tbody = document.createElement("tbody");

  const headerRow = document.createElement("tr");
  const daysOfWeek = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
  for (const day of daysOfWeek) {
    const th = document.createElement("th");
    th.textContent = day;
    headerRow.appendChild(th);
  }
  tbody.appendChild(headerRow);

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

  table.appendChild(tbody);
  calendar.appendChild(table);
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
