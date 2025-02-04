
/**
 * @brief permet de vérifier si deux inputs sont identiques dans leur contenu
 * @param {HTMLElement} element1 premier élément
 * @param {HTMLElement} element2 deuxième élément 
 */
function verifiervaleuregal(element1, element2) {

    if (element1.value != element2.value) {
        element2.className = "form-control border-2 border-danger";
        // ajout du message de prévention
        if (document.getElementById("elementDifferant"+element1.id) == null) {
            let messageDePrevention = document.createElement('p');
            messageDePrevention.className = "text-danger text-start my-2";
            messageDePrevention.id ="elementDifferant"+element1.id;
            messageDePrevention.innerHTML = "les deux mots de passes ne sont pas identique"
            element2.parentNode.appendChild(messageDePrevention);
            }
    } else {
        element2.className = "form-control";
        // suppression du message de prévention
        if(document.getElementById("elementDifferant"+element1.id)!= null){
            let messageDePrevention = document.getElementById("elementDifferant"+element1.id)
            messageDePrevention.remove()
        }
    }

}
/**
 * @brief permet d'ajouter un Listener pour vérifier que deux inputs sont identiques
 * @param {HTMLElement} element1  premier élément
 * @param {HTMLElement} element2  deuxième élément 
 */
function verifieregau(element1, element2) {

    element2.addEventListener("input", function () {
        verifiervaleuregal(element1, element2);
    });


}
/**
 * @brief permet d'afficher un certain nombre de messagers et de cacher le reste  
 * @param {number} nb nombre de messages que l'on souhaite afficher
 */
function cachermessage(nb) {
    
    var compteur = 0;
    for (let i = 0; i < document.getElementById("message").children.length; i++) {
        if (document.getElementById("message").children[i].tagName == "A") {
            compteur++
            // cache les messages en trop
            if (compteur > nb) {
                document.getElementById("message").children[i].style.display = "none"

            }
        }

    }
    // ajout du bouton afficher plus 
    const div = document.createElement("div")
    div.className = "mx-auto w-50 "
    const bouton = document.createElement("button");
    bouton.className = " btn btn-primary mb-4 w-100";
    bouton.id = "afficherplus";
    bouton.innerHTML = "voir plus de message";
    bouton.addEventListener('click', function () { afficherMessage() });
    document.getElementById("message").appendChild(div)
    div.appendChild(bouton)

}

/**
 * @brief permet d'afficher tous les messages cachés 
 */
function afficherMessage() {

    for (let i = 0; i < document.getElementById("message").children.length; i++) {
        if (document.getElementById("message").children[i].tagName == "A") {

            document.getElementById("message").children[i].style.display = ""
        }
    }
    if (document.getElementById("afficherplus") != null) {
        document.getElementById("afficherplus").style.display = "none";
    }
}
/**
 * @brief permet de vérifier si le mail est correct afficher un message de prévention 
 * @param {string} id c'est l'id de l'input que l'on souhaite vérifier 
 */
function verificcationMail(id) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        console.log(element.value.match("[^@\s]+@[^@\s]+\.[^@\s]+"))

        if (element.value.match("[^@\s]+@[^@\s]+\.[^@\s]+") == null) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("mailCorrect"+id) == null) {
                let messageDePrevention = document.createElement('p');
                messageDePrevention.className = "text-danger text-start my-2";
                messageDePrevention.id ="mailCorrect"+id;
                messageDePrevention.innerHTML = "le mail n'est pas correcte"
                element.parentNode.appendChild(messageDePrevention);
                }
        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if(document.getElementById("mailCorrect"+id)!= null){
                let messageDePrevention = document.getElementById("mailCorrect"+id)
                messageDePrevention.remove()
            }
        }

    })
}

/**
 * @brief permet de vérifier si le mot de passe est robuste afficher un message de prévention 
 * @param {string} id c'est l'id de l'input que l'on souhaite vérifier 
 */
function verificcationMDP(id) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        console.log(element.value.match("^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])"))

        if (element.value.match("[a-z]") == null) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("manquanMinuscule"+id) == null) {
            let messageDePrevention = document.createElement('p');
            messageDePrevention.className = "text-danger text-start my-2";
            messageDePrevention.id ="manquanMinuscule"+id;
            messageDePrevention.innerHTML = "il manque une minuscule"
            element.parentNode.appendChild(messageDePrevention);
            }
            console.log("manque minuscule")

        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if(document.getElementById("manquanMinuscule"+id)!= null){
                let messageDePrevention = document.getElementById("manquanMinuscule"+id)
                messageDePrevention.remove()
            }
            
        }
        if (element.value.match("[A-Z]") == null) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("manquanMajuscule"+id) == null) {
            let messageDePrevention = document.createElement('p');
            messageDePrevention.className = "text-danger text-start my-2";
            messageDePrevention.innerHTML = "il manque une majuscule ";
            messageDePrevention.id ="manquanMajuscule"+id;
            element.parentNode.appendChild(messageDePrevention);
            }
            console.log("manque majuscule")
        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if (document.getElementById("manquanMajuscule"+id) != null) {
                let messageDePrevention =document.getElementById("manquanMajuscule"+id);
                messageDePrevention.remove();;
            }
        }
        if (element.value.match("[0-9]") == null) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("manquanChiffre"+id) == null) {
               let messageDePrevention = document.createElement('p');
                messageDePrevention.className = "text-danger text-start my-2";
                messageDePrevention.innerHTML = "il manque un chiffre "
                messageDePrevention.id ="manquanChiffre"+id;
                element.parentNode.appendChild(messageDePrevention);
            }
            console.log("manque chifre ")
        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if (document.getElementById("manquanChiffre"+id) != null) {
                let messageDePrevention =document.getElementById("manquanChiffre"+id);
                messageDePrevention.remove();;
            }
        }
        if (element.value.match("[@$!%*?&]") == null) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("manquanSpeciale"+id) == null) {
            let messageDePrevention = document.createElement('p');
            messageDePrevention.className = "text-danger text-start my-2";
            messageDePrevention.innerHTML = "il manque un caractère spécial" 
            messageDePrevention.id ="manquanSpeciale"+id;
            element.parentNode.appendChild(messageDePrevention);
            }
            console.log("manque cest carte (@,$,!,%,*,?,&)")
        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if (document.getElementById("manquanSpeciale"+id) != null) {
                let messageDePrevention =document.getElementById("manquanSpeciale"+id);
                messageDePrevention.remove();;
            }
        }

    })
}
/**
 * @brief permet de vérifier si le contenu d'un input n'est pas trop court
 * @param {string} id c'est l'id de l'input que l'on souhaite vérifier 
 * @param {number} minlongueur longueur minimale acceptée 
 */
function tropcourt(id, minlongueur) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        if (element.value.length < minlongueur) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("tropCourt"+id) == null) {
                let messageDePrevention = document.createElement('p');
                messageDePrevention.className = "text-danger text-start my-2";
                messageDePrevention.innerHTML = "c'est trop court , il faut au moins " + minlongueur+" caractères";
                messageDePrevention.id ="tropCourt"+id;
                element.parentNode.appendChild(messageDePrevention);
                }
        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if (document.getElementById("tropCourt"+id) != null) {
                let messageDePrevention =document.getElementById("tropCourt"+id);
                messageDePrevention.remove();;
            }
        }
    })




}
/**
 * @brief permet de vérifier si le contenu d'un input n'est pas trop long
 * @param {string} id c'est l'id de l'input que l'on souhaite vérifier 
 * @param {number} minlongueur longueur maximal acceptée 
 */
function troplong(id, maxlongueur) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        if (element.value.length > maxlongueur) {
            element.className = "form-control border-2 border-danger";
            // ajout du message de prévention
            if (document.getElementById("tropLong"+id) == null) {
                let messageDePrevention = document.createElement('p');
                messageDePrevention.className = "text-danger text-start my-2";
                messageDePrevention.innerHTML = "c'est trop long, il faut au max " + maxlongueur+" caractères";
                messageDePrevention.id ="tropLong"+id;
                element.parentNode.appendChild(messageDePrevention);
                }
        } else {
            element.className = "form-control";
            // suppression du message de prévention
            if (document.getElementById("tropLong"+id) != null) {
                let messageDePrevention =document.getElementById("tropLong"+id);
                messageDePrevention.remove();;
            }
        }
    })



}




