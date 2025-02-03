

function verifiervaleuregal(element1, element2) {



    if (element1.value != element2.value) {
        element2.className = "form-control border-2 border-danger";
        if (document.getElementById("manquanMinuscule"+id) == null) {
            let newlabel = document.createElement('p');
            newlabel.className = "text-danger text-start my-2";
            newlabel.id ="manquanMinuscule"+id;
            newlabel.innerHTML = "les deux mots de passes ne sont pas identique"
            element.parentNode.appendChild(newlabel);
            }
    } else {
        element2.className = "form-control";
    }

}

function verifieregau(element1, element2) {

    element2.addEventListener("input", function () {
        verifiervaleuregal(element1, element2);
    });


}

function cachermessage(nb) {

    var compteur = 0;
    for (let i = 0; i < document.getElementById("message").children.length; i++) {
        if (document.getElementById("message").children[i].tagName == "A") {
            compteur++
            if (compteur > nb) {
                document.getElementById("message").children[i].style.display = "none"

            }
        }

    }
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

function verificcationMail(id) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        console.log(element.value.match("[^@\s]+@[^@\s]+\.[^@\s]+"))

        if (element.value.match("[^@\s]+@[^@\s]+\.[^@\s]+") == null) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("mailCorrect"+id) == null) {
                let newlabel = document.createElement('p');
                newlabel.className = "text-danger text-start my-2";
                newlabel.id ="mailCorrect"+id;
                newlabel.innerHTML = "le mail n'est pas correcte"
                element.parentNode.appendChild(newlabel);
                }
        } else {
            element.className = "form-control";
            if(document.getElementById("mailCorrect"+id)!= null){
                let labelMinuscule = document.getElementById("mailCorrect"+id)
                labelMinuscule.remove()
            }
        }

    })
}

function verificcationMDP(id) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        console.log(element.value.match("^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])"))

        if (element.value.match("[a-z]") == null) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("manquanMinuscule"+id) == null) {
            let newlabel = document.createElement('p');
            newlabel.className = "text-danger text-start my-2";
            newlabel.id ="manquanMinuscule"+id;
            newlabel.innerHTML = "il manque une minuscule"
            element.parentNode.appendChild(newlabel);
            }
            console.log("manque minuscule")

        } else {
            element.className = "form-control";
            if(document.getElementById("manquanMinuscule"+id)!= null){
                let labelMinuscule = document.getElementById("manquanMinuscule"+id)
                labelMinuscule.remove()
            }
            
        }
        if (element.value.match("[A-Z]") == null) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("manquanMajuscule"+id) == null) {
            let newlabel = document.createElement('p');
            newlabel.className = "text-danger text-start my-2";
            newlabel.innerHTML = "il manque une majuscule ";
            newlabel.id ="manquanMajuscule"+id;
            element.parentNode.appendChild(newlabel);
            }
            console.log("manque majuscule")
        } else {
            element.className = "form-control";
            if (document.getElementById("manquanMajuscule"+id) != null) {
                let label =document.getElementById("manquanMajuscule"+id);
                label.remove();;
            }
        }
        if (element.value.match("[0-9]") == null) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("manquanChiffre"+id) == null) {
               let newlabel = document.createElement('p');
                newlabel.className = "text-danger text-start my-2";
                newlabel.innerHTML = "il manque un chiffre "
                newlabel.id ="manquanChiffre"+id;
                element.parentNode.appendChild(newlabel);
            }
            console.log("manque chifre ")
        } else {
            element.className = "form-control";
            if (document.getElementById("manquanChiffre"+id) != null) {
                let label =document.getElementById("manquanChiffre"+id);
                label.remove();;
            }
        }
        if (element.value.match("[@$!%*?&]") == null) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("manquanSpeciale"+id) == null) {
            let newlabel = document.createElement('p');
            newlabel.className = "text-danger text-start my-2";
            newlabel.innerHTML = "il manque un carater spéciale " 
            newlabel.id ="manquanSpeciale"+id;
            element.parentNode.appendChild(newlabel);
            }
            console.log("manque cest carte (@,$,!,%,*,?,&)")
        } else {
            element.className = "form-control";
            if (document.getElementById("manquanSpeciale"+id) != null) {
                let label =document.getElementById("manquanSpeciale"+id);
                label.remove();;
            }
        }

    })
}

function tropcourt(id, minlongueur) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        if (element.value.length < minlongueur) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("tropCourt"+id) == null) {
                let newlabel = document.createElement('p');
                newlabel.className = "text-danger text-start my-2";
                newlabel.innerHTML = "c'est trop court , il faut au moins " + minlongueur+" caractères";
                newlabel.id ="tropCourt"+id;
                element.parentNode.appendChild(newlabel);
                }
        } else {
            element.className = "form-control";
            if (document.getElementById("tropCourt"+id) != null) {
                let label =document.getElementById("tropCourt"+id);
                label.remove();;
            }
        }
    })




}
function troplong(id, maxlongueur) {
    let element = document.getElementById(id);
    element.addEventListener("input", function () {
        if (element.value.length > maxlongueur) {
            element.className = "form-control border-2 border-danger";
            if (document.getElementById("tropLong"+id) == null) {
                let newlabel = document.createElement('p');
                newlabel.className = "text-danger text-start my-2";
                newlabel.innerHTML = "c'est trop long, il faut au max " + maxlongueur+" caractères";
                newlabel.id ="tropLong"+id;
                element.parentNode.appendChild(newlabel);
                }
        } else {
            element.className = "form-control";
            if (document.getElementById("tropLong"+id) != null) {
                let label =document.getElementById("tropLong"+id);
                label.remove();;
            }
        }
    })



}




