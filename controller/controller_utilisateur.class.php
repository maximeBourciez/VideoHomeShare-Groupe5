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
        
        $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
        $mdp = isset($_POST['pwd']) ? $_POST['pwd'] : null;

        
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        //print("coucou");
        if ($managerutilisateur->findByMailandPWD($mail,$mdp) == 1){
            //Génération de la vue
            $template = $this->getTwig()->load('index.html.twig');
            echo $template->render(array(
                'description' => "Je fais mes tests"
            ));
            print ("vous êtes connecté");
        }else{
            //Génération de la vue
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array(
                'description' => "Je fais mes tests"
            ));
            
        }


    }
    /**
     * @brief vérifie les informations saisies lors de l'inscription et crée un utilisateur si les informations sont correctes et renvoie sur la page de connection   sinon renvoie sur la page d'inscription
     *
     * @return void
     */
    public function checkInfoInscription(){

        //réccupération des données du formulaire
        $id = isset($_POST['idantifiant']) ? $_POST['idantifiant'] : null;
        $pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : null;
        $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
        $mdp = isset($_POST['mdp']) ? $_POST['mdp'] : null;
        $vmdp = isset($_POST['vmdp']) ? $_POST['vmdp'] : null;
        $nom = isset($_POST['nom']) ? $_POST['nom'] : null;

        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        if ( strlen($pseudo) <= 50 && strlen($id) <= 20 && strlen($mail) <= 320 && strlen($mdp) <= 30 && strlen($vmdp) <= 30 && strlen($nom) <= 50){ 
            // verifier si l'id n'est pas déjà utilisé
            if ( $managerutilisateur->find($id) == null){
                // verifier si le mail est valide 
                if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    // verifier si le mail n'est pas déjà utilisé
                    if ($managerutilisateur->findByMail($mail) == null){
                        // verifier si les deux mots de passe sont identiques
                        if ($mdp == $vmdp){ 
                            //création de l'utilisateur
                            $newUtilisateur = new Utilisateur ($id,$pseudo , $nom ,$mail,$mdp,"Utilisateur",NULL,NULL);
                            $managerutilisateur->create($newUtilisateur);
                            //Génération de la vue
                            $template = $this->getTwig()->load('connection.html.twig');
                            echo $template->render(array(
                                'description' => "Je fais mes tests"
                            ));
                            return;
                        }
                    }
                }
            }
        }
        //erreur de saisie
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(array(
            'description' => "Je fais mes tests"
        ));          
    }

    /**
     * @brief  permet d'afficher la page ou l'utilisateur rensegner son adre mail pour changer le mot de passe
     * 
     * @return void
     */
    public function afficherpageMDPOulier() : void {

        $template = $this->getTwig()->load('motDePasseOublie.html.twig');
        echo $template->render(array());  
        
    }
    /**
     * @brief envoie un mail à l'utilisateur pour qu'il puisse changer son mot de passe
     * 
     * @return void
     */
    public function envoieMailMDPOublie() : void {
        $mail = isset($_POST['email']) ? $_POST['email'] : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->findByMail($mail);
        if ($utilisateur != null){
            $id = $utilisateur->getId();
            var_dump($mail);
            $message = "Bonjour, \n\n Vous avez demandé à réinitialiser votre mot de passe. Voici votre mot de passe : ".URL_SITE."index.php?controller=utilisateur&methode=changerMDP&id=".$id." \n\n Cordialement, \n\n L'équipe de la plateforme de vhs";
            $test = mail($mail, "Réinitialisation de votre mot de passe", $message);
            print($test);
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array());
        }else{
            //gener un popoup pour dire que le mail n'est pas valide

            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array());  
        }
    }



    
}