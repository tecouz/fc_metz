// Fonction pour afficher ou masquer les champs de tri en fonction de la sélection de la compétition
function toggleSortingFields() {
  var competitionSelect = document.getElementById("competition");
  var sortingFields = document.getElementsByClassName("sorting-field");

  // Vérifier si une compétition est sélectionnée
  if (competitionSelect.value !== "") {
    // Afficher les champs de tri
    for (var i = 0; i < sortingFields.length; i++) {
      sortingFields[i].style.display = "inline-block";
    }
  } else {
    // Masquer les champs de tri
    for (var i = 0; i < sortingFields.length; i++) {
      sortingFields[i].style.display = "none";
    }
  }
}

// Ajouter un écouteur d'événement au chargement de la page et au changement de la sélection de compétition
document.addEventListener("DOMContentLoaded", toggleSortingFields);
document
  .getElementById("competition")
  .addEventListener("change", toggleSortingFields);
