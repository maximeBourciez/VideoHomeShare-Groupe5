function onYouTubeIframeAPIReady() { // Crée un objet YT.Player pour intégrer le lecteur YouTube
   
    var  player = new YT.Player('video', {
        height: 180,
        width: 320,
        videoId: 'dQw4w9WgXcQ',
        playerVars: {
            autoplay: 1  // L'autoplay est activé avec la valeur 1
        }, 
        events: {
        'onReady': function(event) {
            onPlayerReady(event, id);  // Passe des paramètres à onPlayerReady
        },
        'onStateChange': onPlayerStateChange
        }
    });
    
    }

    // Fonction appelée lorsque le lecteur est prêt
    function onPlayerReady(event, id) { // Vous pouvez maintenant utiliser les fonctions de l'API pour contrôler le lecteur
        event.target.playVideo(); // Joue la vidéo
        console.log(event.target.getVideoData())
        setInterval(function(){majVideo(id, event.target)},1000);
        if (hote == true){
            setInterval(function(){envoyerinfoVideo(id, event.target)},1000);
        }

        event.target.setSize(document.getElementById("description").clientWidth,document.getElementById("description").clientWidth*9/16);
        
        window.addEventListener('resize', function() {
				
            event.target.setSize(document.getElementById("description").clientWidth,document.getElementById("description").clientWidth*9/16)
    
            });

        
        majdescription(event.target);

        document.getElementById("ajouter").addEventListener("click", function() {
            ajouerVideo(id , event.target);
        });
        document.getElementById("suivante").addEventListener("click", function() {
            prochainVideo(id, event.target);
        }); 
        
        document.getElementById("Message").addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                envoyerMessage(id);
            }
        });
        
        


   
        
    }

    function majdescription(player) {

        document.getElementById("dureeVideo").innerHTML = "duree : "+ Math.floor(player.getDuration() / 60) + "min " + Math.round(player.getDuration() % 60);
        document.getElementById("titreVideo").innerHTML = "titre : "+player.videoTitle;
        document.getElementById("lienVideo").innerHTML = "lien video : "+player.getVideoUrl();

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
            console.log(url);
            var videoId = new URL(result).searchParams.get("v");
            
            if (videoId == null) {
                videoId = new URL(result).pathname.split('/')[1];
                
            }
            player.loadVideoById(videoId);
                  
             
            
            
          });
       
          
    }

    function ajouerVideo(id, player) {
        let url = document.getElementById("lien").value;
        if(filter_var($url, FILTER_VALIDATE_URL) !== false){
        callController('salle', 'ajouterVideo', [["id", id],["url", url] ]);
        }
        document.getElementById("lien").value = '';
        majVideo(id, player)
        
        
    }

    function majchat(id) {
        let url = callController('salle', 'majChat', [["id", id]]);
        url.then(result =>  {
            if (result[0] == "{") {
            let data = JSON.parse(result);
            let messages = data.chat.messages;
            
            
            for (let index = 1; index < messages.length+1; index++) {

                if( document.getElementById("message"+messages[index-1].id) == null) {
                    let newDiv = document.createElement("div");
                    newDiv.id = "message"+messages[index-1].id;
                    newDiv.className = "bg-white rounded my-3 py-2 d-inline-block ";
                    let br = document.createElement("br");
                    let puseur = document.createElement("p");
                    puseur.className = "mb-0 mx-2";
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
        }else{
            window.location.href ="index.php?controller=salle&methode=accueilWatch2Gether";
        }
        });
    }

    function envoyerMessage(id) {
        let message = document.getElementById("Message").value;
        if (message != "") {
              
        callController('salle', 'envoyerMessage', [["id", id],["message", message]]);
        document.getElementById("Message").value = '';
        }
        majchat(id);
    }

    function majVideo(id, player) {
        let url = callController('salle', 'majVideo', [["id", id]]);
        url.then(result => {
            if (result[0] == "{") {
            let data = JSON.parse(result);
            let url = data.video.url;
            let urllast = player.getVideoUrl();
            var videoId = new URL(url).searchParams.get("v");
            var videoIdlast = new URL(urllast).searchParams.get("v");
            
            if (videoId == null) {
                videoId = new URL(url).pathname.split('/')[1];
                
            }
            if (videoIdlast == null) {
                videoIdlast = new URL(urllast).pathname.split('/')[1];
                
            }
            if (videoId != videoIdlast) {
            player.loadVideoById(videoId); 
            majdescription(player);
            }
            let etat = data.video.etat;
            

            if (etat == 1 ) {
                player.playVideo();
            } else {
                player.pauseVideo();
            }

            if( etat != player.getPlayerState()) {
           
            let temps = data.video.temps;
            player.seekTo(temps);
            
            
            }

            
    
        }

            
            

        });
    }

    function envoyerinfoVideo(id, player) {
        
    
       let url = player.getVideoUrl();
    
       let cc = url.replace("&", "\\");
       let test = callController('salle', 'envoyerinfoVideo', [["id", id],["temps", player.getCurrentTime()],["etat", player.getPlayerState()],["url", cc]]);
       
    }


