function onYouTubeIframeAPIReady(w= 633) { // Crée un objet YT.Player pour intégrer le lecteur YouTube
    console.log(w);
    let player = new YT.Player('video', {
        height: w*9/16,
          width: w,
        videoId: 'gIdaTOTzZuw', // Remplacez VIDEO_ID par l'ID de votre vidéo YouTube
        events: {
        'onReady': onPlayerReady
        }
    });
    return(player);
    }

    // Fonction appelée lorsque le lecteur est prêt
    function onPlayerReady(event) { // Vous pouvez maintenant utiliser les fonctions de l'API pour contrôler le lecteur
    event.target.playVideo(); // Joue la vidéo
    document.getElementById("dureeVideo").innerHTML += Math.floor(event.target.getDuration() / 60) + "min " + event.target.getDuration() % 60;
    document.getElementById("titreVideo").innerHTML += event.target.videoTitle;
    document.getElementById("lienVideo").innerHTML += event.target.getVideoUrl();
                    
    }

      

    async function callController(controller, methode, parametres) {
        try {

            var parm ='';
            for (let index = 0; index < parametres.length; index++) {
                 parm += `&${parametres[index][0]}=${parametres[index][1]}`;
                
            }
        
            // Construire l'URL avec les paramètres
            const url = `index.php?controller=${controller}&methode=${methode}${parm}`;

            // Envoyer une requête fetch
            const response = await fetch(url, {
                method: 'GET', // Utilisez POST si nécessaire
            });

            // Vérifier si la requête a réussi
            if (response.ok) {
                const result = await response.text(); // Lire la réponse en texte
                console.log(result) ;
            } else {
                console.error('Erreur HTTP :', response.status);
            }
        } catch (error) {
            console.error('Erreur lors de la requête Fetch :', error);
        }
    }


