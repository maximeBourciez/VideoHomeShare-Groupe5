// JS/loadmore.js

document.addEventListener("DOMContentLoaded", function () {
  initLoadMoreButtons();
});

function initLoadMoreButtons() {
  // Initialise les boutons "Voir plus"
  document.querySelectorAll(".load-more-btn").forEach((button) => {
    button.addEventListener("click", handleLoadMore);
  });

  // Initialise les boutons "Voir moins"
  document.querySelectorAll(".load-less-btn").forEach((button) => {
    button.classList.add("d-none"); // Cache initialement les boutons "Voir moins"
    button.addEventListener("click", handleLoadLess);
  });
}

function handleLoadMore(event) {
  const button = event.currentTarget;
  const containerId = button.dataset.container;
  const container = document.getElementById(containerId);
  const contentType = containerId.split("-")[0];

  const total = parseInt(button.dataset.total);
  let loaded = parseInt(button.dataset.loaded);

  // Charge 5 éléments supplémentaires
  loadMoreItems(container, contentType, loaded, total);

  // Met à jour le compteur
  loaded += 5;
  button.dataset.loaded = loaded;

  // Affiche le bouton "Voir moins"
  const loadLessBtn = container.parentElement.querySelector(".load-less-btn");
  loadLessBtn.classList.remove("d-none");

  // Cache le bouton "Voir plus" si tous les éléments sont chargés
  if (loaded >= total) {
    button.classList.add("d-none");
  }
}

function handleLoadLess(event) {
  const button = event.currentTarget;
  const containerId = button.dataset.container;
  const container = document.getElementById(containerId);
  const moreButton = container.parentElement.querySelector(".load-more-btn");

  // Récupère tous les éléments chargés
  const items = container.querySelectorAll(".content-item");

  // Garde les 5 premiers éléments et supprime les autres
  items.forEach((item, index) => {
    if (index >= 5) {
      item.remove();
    }
  });

  // Réinitialise le compteur du bouton "Voir plus"
  moreButton.dataset.loaded = "5";

  // Affiche le bouton "Voir plus" et cache le bouton "Voir moins"
  moreButton.classList.remove("d-none");
  button.classList.add("d-none");
}

function loadMoreItems(container, contentType, startIndex, total) {
  const template = document.getElementById(`${contentType}-template`);
  const hiddenItems = template.querySelectorAll(".content-item.d-none");

  let itemsAdded = 0;
  hiddenItems.forEach((item, index) => {
    if (
      index >= startIndex &&
      itemsAdded < 5 &&
      startIndex + itemsAdded < total
    ) {
      const clone = item.cloneNode(true);
      clone.classList.remove("d-none");
      clone.className = "content-item bg-mydark my-2 shadow rounded";

      if (startIndex + itemsAdded < total - 1) {
        const separator = document.createElement("hr");
        separator.className = "text-white m-0 p-0";
        clone.appendChild(separator);
      }

      container.appendChild(clone);
      itemsAdded++;
    }
  });
}
