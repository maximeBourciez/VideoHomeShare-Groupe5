const aujourdHui = new Date();
aujourdHui.setDate(aujourdHui.getDate() + 1);

// On extrait les informations nécessaires et on les formate sous forme de chaîne "YYYY-MM-DD"
const annee = aujourdHui.getFullYear();
const mois = String(aujourdHui.getMonth() + 1).padStart(2, '0'); // Mois sur 2 chiffres
const jour = String(aujourdHui.getDate()).padStart(2, '0'); // Jour sur 2 chiffres

const dateFormatee = `${annee}-${mois}-${jour}`;

// On définit la valeur minimale pour le champ de date
Array.from(document.getElementsByName("dateF")).forEach(element => {
    element.min = dateFormatee;
});


