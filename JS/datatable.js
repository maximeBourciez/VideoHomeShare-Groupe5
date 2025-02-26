$(document).ready(function () {
  // Fonction pour déterminer le type d'appareil
  function getDeviceType() {
    const width = $(window).width();
    if (width <= 576) return "mobile";
    if (width <= 992) return "tablet";
    return "desktop";
  }

  // Fonction pour le rendu du titre avec adaptations selon l'appareil
  function renderTitleColumn(data, type, row, meta) {
    if (type === "display") {
      const deviceType = getDeviceType();

      if (deviceType === "mobile") {
        // Format compact pour mobile
        return `
          <div class="episode-mobile-info">
            <div class="episode-title">${data}</div>
            <div class="episode-details">S${row[1]} E${row[2]} - ${row[4]}</div>
            <div class="episode-desc">${row[5]}</div>
          </div>
        `;
      } else if (deviceType === "tablet") {
        // Format optimisé pour tablette - moins d'infos mais plus lisible
        return `
          <div class="episode-tablet-info">
            <div class="episode-title fw-bold">${data}</div>
            <div class="episode-subtitle">S${row[1]} E${row[2]}</div>
          </div>
        `;
      }
    }
    return data;
  }

  // Fonction pour le rendu de la date avec formatage BEAUCOUP plus visible
  function renderDateColumn(data, type, row, meta) {
    if (type === "display") {
      const deviceType = getDeviceType();

      if (deviceType === "tablet") {
        // Format très visible pour tablette
        return `<div class="date-tablet-container"><span class="date-tablet">${data}</span></div>`;
      }
    }
    return data;
  }

  // Initialisation de la DataTable
  const table = $("#episodesTable").DataTable({
    responsive: true,
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json",
    },
    order: [
      [1, "asc"],
      [2, "asc"],
    ],
    pageLength: 15,
    columnDefs: [
      {
        // Colonne image
        targets: 0,
        width: "90px",
        orderable: false,
        responsivePriority: 1,
      },
      {
        // Saison
        targets: 1,
        width: "60px",
        responsivePriority: 3,
        className: "desktop", // Visible uniquement sur desktop
      },
      {
        // Épisode
        targets: 2,
        width: "60px",
        responsivePriority: 4,
        className: "desktop", // Visible uniquement sur desktop
      },
      {
        // Titre
        targets: 3,
        responsivePriority: 1,
        render: renderTitleColumn,
        width: "40%", // Augmenté pour tablette
      },
      {
        // Date
        targets: 4,
        responsivePriority: 1, // Priorité augmentée à 1
        className: "min-tablet all", // Toujours visible
        render: renderDateColumn,
        width: "20%", // Plus d'espace pour la date
      },
      {
        // Description
        targets: 5,
        responsivePriority: 5,
        className: "none", // Masquée sur tablette et mobile
      },
    ],
    // Configuration du DOM
    dom:
      '<"row mx-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
      '<"row"<"col-sm-12"tr>>' +
      '<"row mx-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    // Initialisation
    initComplete: function () {
      // Ajout immédiat des styles CSS pour l'affichage tablette
      $("<style>")
        .prop("type", "text/css")
        .html(
          `
          /* Styles globaux pour la table */
          #episodesTable td {
            vertical-align: middle;
          }
          
          /* Styles spécifiques pour tablette */
          @media (min-width: 577px) and (max-width: 992px) {
            .date-tablet-container {
              padding: 6px 4px;
              text-align: center;
              display: block;
            }
            
            .date-tablet {
              font-weight: bold;
              font-size: 1.1rem !important;
              white-space: nowrap;
              padding: 4px 8px;
              background-color: rgba(255, 255, 255, 0.1);
              border-radius: 4px;
              display: inline-block;
              min-width: 100px;
              text-align: center;
            }
            
            .episode-tablet-info {
              padding: 8px 0;
            }
            
            .episode-title {
              font-size: 1.05rem;
              margin-bottom: 4px;
            }
            
            .episode-subtitle {
              font-size: 0.9rem;
              color: #aaa;
            }
            
            /* Ajustement spécifique pour la colonne de date */
            #episodesTable td:nth-child(5) {
              min-width: 120px !important;
              width: auto !important;
              text-align: center;
            }
            
            /* Augmentation des espacements entre cellules */
            #episodesTable td {
              padding: 15px 8px;
            }
          }
        `
        )
        .appendTo("head");

      $("#episodesTable_wrapper").addClass("text-white");
      $(
        ".dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate"
      ).addClass("text-white");

      // Force un recalcul des colonnes après l'ajout des styles
      setTimeout(function () {
        table.columns.adjust().responsive.recalc();
      }, 100);
    },
  });

  // Gestion du redimensionnement avec debounce
  let resizeTimer;
  $(window).on("resize", function () {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function () {
      // Force le redessin complet du tableau
      table.columns.adjust().responsive.recalc().draw(false);
    }, 250);
  });

  // Ajout d'une classe au body pour faciliter le ciblage CSS
  $("body").addClass("datatables-responsive");

  // Application immédiate des changements
  setTimeout(function () {
    table.columns.adjust().responsive.recalc();
  }, 200);
});
