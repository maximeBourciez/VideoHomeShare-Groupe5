<?php


class ControllerUtilisateur extends Controller{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }


    
    public function connexion() : void{
    
        //Génération de la vue
        $template = $this->getTwig()->load('connection.html.twig');
        echo $template->render(array(
            'description' => "Je fais mes tests"
        ));
                

    }

    public function inscription() : void{
    
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(array(
            'description' => "Je fais mes tests"
        ));
                

    }
    public function checkInfoConnecter(){


    }
    public function checkInfoInscription(){

        
    }


}