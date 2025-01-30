document.addEventListener("DOMContentLoaded", function () {
  initLoadMoreButtons();
});

function initLoadMoreButtons() {
  document.querySelectorAll(".load-more-btn").forEach((button) => {
    button.addEventListener("click", handleLoadMore);
  });

  document.querySelectorAll(".load-less-btn").forEach((button) => {
    button.addEventListener("click", handleLoadLess);
  });
}

function handleLoadMore(event) {
  const button = event.currentTarget;
  const container = document.getElementById(button.dataset.container);
  if (!container) return;

  const total = parseInt(button.dataset.total);
  let loaded = parseInt(container.dataset.loaded);
  const itemsToShow = button.dataset.type === "thread" ? 3 : 5; // Threads : 3, Films : 5
  const hiddenItems = container.querySelectorAll(
    ".content-item.d-none, .thread-item.d-none"
  );

  for (let i = 0; i < Math.min(itemsToShow, hiddenItems.length); i++) {
    hiddenItems[i].classList.remove("d-none");
  }

  loaded += itemsToShow;
  container.dataset.loaded = loaded;
  button.dataset.loaded = loaded;

  const loadLessBtn = button.parentElement.querySelector(".load-less-btn");
  if (loadLessBtn) {
    loadLessBtn.classList.remove("d-none");
  }

  if (loaded >= total) {
    button.classList.add("d-none");
  }
}

function handleLoadLess(event) {
  const button = event.currentTarget;
  const container = document.getElementById(button.dataset.container);
  if (!container) return;

  const moreButton = button.parentElement.querySelector(".load-more-btn");
  if (!moreButton) return;

  let loaded = parseInt(container.dataset.loaded);
  const baseVisible = button.dataset.type === "thread" ? 3 : 5; // 3 pour les threads, 5 pour les films

  const visibleItems = Array.from(
    container.querySelectorAll(
      ".thread-item:not(.d-none), .content-item:not(.d-none)"
    )
  );

  // Déterminer combien d'éléments on doit cacher
  const itemsToHide = Math.max(0, loaded - baseVisible);

  for (let i = 0; i < itemsToHide; i++) {
    const index = visibleItems.length - 1 - i;
    if (index >= baseVisible) {
      visibleItems[index].classList.add("d-none");
    }
  }

  loaded -= itemsToHide;
  container.dataset.loaded = loaded;
  moreButton.dataset.loaded = loaded;

  // Réafficher le bouton "Afficher plus" si nécessaire
  moreButton.classList.remove("d-none");

  // Cacher le bouton "Afficher moins" si le nombre d'éléments affichés est revenu à la base
  if (loaded <= baseVisible) {
    button.classList.add("d-none");
  }

  if (container) {
    container.scrollIntoView({ behavior: "smooth", block: "start" });
  }
}
