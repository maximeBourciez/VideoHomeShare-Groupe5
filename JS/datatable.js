$(document).ready(function () {
  $("#episodesTable").DataTable({
    responsive: {
      details: {
        type: "column",
        target: "tr",
        renderer: function (api, rowIdx, columns) {
          // Créer un contenu détaillé pour l'affichage mobile
          var data = $.map(columns, function (col, i) {
            // Ne pas inclure la colonne d'image ou les colonnes vides dans les détails
            if (col.hidden && col.data && i !== 0) {
              return (
                '<div class="episode-detail-item">' +
                '<span class="episode-detail-label">' +
                col.title +
                ":</span> " +
                '<span class="episode-detail-value">' +
                col.data +
                "</span>" +
                "</div>"
              );
            }
            return "";
          }).join("");

          return data
            ? '<div class="episode-details-container">' + data + "</div>"
            : false;
        },
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
    dom:
      '<"row mx-2 text-white"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
      '<"row"<"col-sm-12"tr>>' +
      '<"row mx-2 text-white"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    columnDefs: [
      // Image toujours visible
      {
        targets: 0,
        responsivePriority: 1, // Priorité maximale pour toujours afficher l'image
        width: "15%",
        orderable: false,
      },
      // Titre toujours visible
      {
        targets: 3,
        responsivePriority: 2, // Haute priorité pour le titre
        width: "19%",
      },
      // Colonnes moins importantes
      {
        targets: [1, 2, 4, 5],
        responsivePriority: 3, // Ces colonnes disparaîtront en premier
      },
    ],
    // Initialisation
    initComplete: function () {
      $("#episodesTable_wrapper").addClass("text-white");
      $(".dataTables_length").addClass("text-white");
      $(".dataTables_filter").addClass("text-white");
      $(".dataTables_info").addClass("text-white");
      $(".dataTables_paginate").addClass("text-white");
    },
  });
});
