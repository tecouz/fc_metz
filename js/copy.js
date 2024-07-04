document.addEventListener("DOMContentLoaded", function () {
  // Attendre que le DOM soit complètement chargé avant d'exécuter le code

  // Fonction pour copier le texte dans le presse-papiers
  function copyToClipboard(text) {
    const textarea = document.createElement("textarea");
    textarea.value = text;
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    document.body.removeChild(textarea);
    console.log("Texte copié dans le presse-papiers");
  }

  // Gestionnaire d'événement pour le bouton "Copier l'identifiant"
  const copyUsernameBtn = document.getElementById("copy-username");
  if (copyUsernameBtn) {
    copyUsernameBtn.addEventListener("click", () => {
      const usernameElement = document.querySelector(
        ".login-info p:first-child strong"
      );
      if (usernameElement) {
        const username = usernameElement.nextSibling.textContent.trim();
        copyToClipboard(username);
      } else {
        console.error("L'élément contenant l'identifiant n'a pas été trouvé.");
      }
    });
  } else {
    console.error("Le bouton 'Copier l'identifiant' n'a pas été trouvé.");
  }

  // Gestionnaire d'événement pour le bouton "Copier le mot de passe"
  const copyPasswordBtn = document.getElementById("copy-password");
  if (copyPasswordBtn) {
    copyPasswordBtn.addEventListener("click", () => {
      const passwordInput = document.getElementById("password-input");
      if (passwordInput) {
        const password = passwordInput.value;
        copyToClipboard(password);
      } else {
        console.error(
          "L'élément contenant le mot de passe n'a pas été trouvé."
        );
      }
    });
  } else {
    console.error("Le bouton 'Copier le mot de passe' n'a pas été trouvé.");
  }
});
