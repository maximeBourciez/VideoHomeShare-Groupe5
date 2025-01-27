 function onYouTubeIframeAPIReady() { // Crée un objet YT.Player pour intégrer le lecteur YouTube
    
    var  player = new YT.Player('video', {
        height: 180,
        width: 320,
        videoId: '_SjWyd7LxZ8', // Remplacez VIDEO_ID par l'ID de votre vidéo YouTube_SjWyd7LxZ8
        events: {
        'onReady': onPlayerReady,
        'onStateChange': onPlayerStateChange
        }
    });

    
        
    return(player);
    }

    // Fonction appelée lorsque le lecteur est prêt
    function onPlayerReady(event) { // Vous pouvez maintenant utiliser les fonctions de l'API pour contrôler le lecteur
        event.target.playVideo(); // Joue la vidéo
        event.target.setSize(document.getElementById("description").clientWidth,document.getElementById("description").clientWidth*9/16)
        majVideo(1, event.target);
        majchat(1);
        window.addEventListener('resize', function() {
				
            event.target.setSize(document.getElementById("description").clientWidth,document.getElementById("description").clientWidth*9/16)
    
            })
                    
    }

    function majdescription(player) {
        console.log(player);
        document.getElementById("dureeVideo").innerHTML = "duree : "+ Math.floor(player.getDuration() / 60) + "min " + player.getDuration() % 60;
        document.getElementById("titreVideo").innerHTML = "titre : "+player.videoTitle;
        document.getElementById("lienVideo").innerHTML = "lien video : "+player.target.getVideoUrl();

    }

      

    async function callController(controller, methode, parametres) {
        try {

            var parm ='';
            for (let index = 0; index < parametres.length; index++) {
                 parm += `&${parametres[index][0]}=${parametres[index][1]}`;
                
            }
        
            // Construire l'URL avec les paramètres
            const url = `index.php?controller=${controller}&methode=${methode}${parm}`;
            console.log(url);
            // Envoyer une requête fetch
            const response = await fetch(url, {
                method: 'GET', // Utilisez POST si nécessaire
            });

            // Vérifier si la requête a réussi
            if (response.ok) {
                const result = await response.text(); // Lire la réponse en texte
                return(result) ;
            } else {
                console.error('Erreur HTTP :', response.status);
            }
        } catch (error) {
            console.error('Erreur lors de la requête Fetch :', error);
        }
    }

    function prochainVideo( id , player) {
        
        
       let url = callController('salle', 'prochainVideo', [["id", id]]);
         url.then(result => {
            
            var videoId = new URL(result).searchParams.get("v");
            
            if (videoId == null) {
                videoId = new URL(result).pathname.split('/')[1];
                
            }
            player.loadVideoById(videoId);
            
            
            envoyerinfoVideo(id, player);
             
            
            
          });
       
          
    }

    function ajouerVideo(id, player) {
        let url = document.getElementById("lien").value;
        
        let test =callController('salle', 'ajouterVideo', [["id", id],["url", url] ]);
        document.getElementById("lien").value = '';
        majVideo(id, player)
       console.log(test);        
        
    }

    function majchat(id) {
        let url = callController('salle', 'majChat', [["id", id]]);
        url.then(result =>  {
            
           
            let data = JSON.parse(result);
            let messages = data.chat.messages;
            
            
            for (let index = 1; index < messages.length+1; index++) {

                if( document.getElementById("message"+messages[index-1].id) == null) {
                    let newDiv = document.createElement("div");
                    newDiv.id = "message"+messages[index-1].id;
                    newDiv.className = "bg-white rounded my-3 py-2 d-inline-block ";
                    let br = document.createElement("br");
                    let puseur = document.createElement("p");
                    puseur.className = "mb-0 ms-2";
                    puseur.innerHTML = messages[index-1].auteur;
                    let pmessage = document.createElement("p");
                    pmessage.className = "mb-0 mx-2";
                    pmessage.innerHTML = messages[index-1].message;
                    newDiv.appendChild(puseur);
                    newDiv.appendChild(pmessage);
                    document.getElementById("messages").appendChild(newDiv);
                    document.getElementById("messages").appendChild(br);
                    
                }
                
            }
        });
    }

    function envoyerMessage(id) {
        let message = document.getElementById("Message").value;
        console.log(message);
        callController('salle', 'envoyerMessage', [["id", id],["message", message]]);
        document.getElementById("Message").value = '';
        
        majchat(id);
    }

    function majVideo(id, player) {
        let url = callController('salle', 'majVideo', [["id", id]]);
        url.then(result => {
            let data = JSON.parse(result);
            let url = data.video.url;
            var videoId = new URL(url).searchParams.get("v");
            
            if (videoId == null) {
                videoId = new URL(url).pathname.split('/')[1];
                
            }
            console.log(videoId);
            player.loadVideoById(videoId); 
            let etat = data.video.etat;
            if (etat == 1 ) {
                player.playVideo();
            } else {
                player.pauseVideo();
            }

            let temps = data.video.temps;
            player.seekTo(temps);
            

        });
    }

    function envoyerinfoVideo(id, player) {
        console.log(player);
        console.log(player.getVideoUrl());
       let url = player.getVideoUrl();
       console.log(url);
       let cc = url.replace("&", "\\");
       let test = callController('salle', 'envoyerinfoVideo', [["id", id],["temps", player.getCurrentTime()],["etat", player.getPlayerState()],["url", cc]]);
        console.log(test);
    }


