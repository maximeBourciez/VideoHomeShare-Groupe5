

function verifiervaleuregal ( element1 , element2 ){
   


    if (element1.value != element2.value ) {
        element2.className  = "form-control border-2 border-danger";
    }else
    {
        element2.className  = "form-control";   
    }

}

function verifieregau( element1 , element2 ){
    
    element2.addEventListener("input", function() {
        verifiervaleuregal(element1, element2);
    });


}

function cachermessage(){

    var compteur =0;
    for (let i = 0; i < document.getElementById("message").children.length; i++) {
        if (document.getElementById("message").children[i].tagName == "A" ){
            compteur ++
            if (compteur > 6){
                document.getElementById("message").children[i].style.display = "none"

            }
        }
        
    }
    const div = document.createElement("div")
    div.className ="mx-auto w-50 "
    const bouton = document.createElement("button");
    bouton.className = " btn btn-primary mb-4 w-100";
    bouton.id = "afficherplus";
    bouton.innerHTML = "voir plus de message";
    bouton.addEventListener('click',function(){afficherMessage()} );
    document.getElementById("message").appendChild(div)
    div.appendChild(bouton)

}

function afficherMessage() {
    
    for (let i = 0; i < document.getElementById("message").children.length; i++) {
        if (document.getElementById("message").children[i].tagName == "A" ){

            document.getElementById("message").children[i].style.display = ""
            
        }
        
    }
    if( document.getElementById("afficherplus") != null){
    document.getElementById("afficherplus").style.display = "none";
    }
}





