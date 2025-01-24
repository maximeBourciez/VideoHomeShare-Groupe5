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

    function changetailleVideo(player){

        player.target;
    }


