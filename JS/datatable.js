$(document).ready(function () {
  const table = $("#episodesTable").DataTable({
    responsive: {
      details: {
        type: "column",
        target: "tr",
      },
    },
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/fr-FR.json",
    },
    order: [
      [1, "asc"],
      [2, "asc"],
    ],
    pageLength: 15,

    // Configuration des colonnes
    columnDefs: [
      {
        // Colonne image
        targets: 0,
        width: "100px",
        orderable: false,
        responsivePriority: 1,
      },
      {
        // Saison
        targets: 1,
        width: "80px",
        responsivePriority: 3,
      },
      {
        // Épisode
        targets: 2,
        width: "80px",
        responsivePriority: 4,
      },
      {
        // Titre
        targets: 3,
        responsivePriority: 1,
        render: function (data, type, row) {
          if (type === "display") {
            const mobileView = window.innerWidth <= 768;
            if (mobileView) {
              return `
                <div class="episode-mobile-info">
                  <div class="episode-title">${data}</div>
                  <div class="episode-details">S${row[1]} E${row[2]} - ${row[4]}</div>
                  <div class="episode-desc">${row[5]}</div>
                </div>
              `;
            }
            return data;
          }
          return data;
        },
      },
      {
        // Date
        targets: 4,
        responsivePriority: 2,
      },
      {
        // Description
        targets: 5,
        responsivePriority: 5,
      },
    ],

    // Configuration du DOM
    dom:
      '<"row mx-2"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
      '<"row"<"col-sm-12"tr>>' +
      '<"row mx-2"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',

    // Initialisation
    initComplete: function () {
      this.api().columns.adjust().responsive.recalc();

      // Ajout des classes pour le style
      $("#episodesTable_wrapper").addClass("text-white");
      $(
        ".dataTables_length, .dataTables_filter, .dataTables_info, .dataTables_paginate"
      ).addClass("text-white");
    },
  });

  // Gestion du redimensionnement
  $(window).on("resize", function () {
    table.columns.adjust().responsive.recalc();
  });
});
