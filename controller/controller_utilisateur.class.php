<?php


class ControllerUtilisateur extends Controller{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }


    /**
     * @bref permet d'afficher la page de connection
     *
     * @return void
     */
    public function connexion() : void{
    
        //Génération de la vue
        $template = $this->getTwig()->load('connection.html.twig');
        echo $template->render(array(
            'description' => "Je fais mes tests"
        ));
                

    }

    /**
     * @brief permet d'afficher la page d'inscription
     *
     * @return void
     */
    public function inscription() : void{
    
        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(array(
            'description' => "Je fais mes tests"
        ));
                

    }

    /**
     * @brief vérifie les informations saisies lors de la connection et connecte l'utilisateur et revoit sur la page d'acceuil si les informations sont correctes  sinon renvoie sur la page de connection
     *
     * @return void
     */
    public function checkInfoConnecter(){
        
        $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
        $mdp = isset($_POST['pwd']) ? $_POST['pwd'] : null;
        
        $mail = str_replace(' ', '', $mail);
        
        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        $utilisateur = $managerutilisateur->findByMail($mail);
    
        if ($utilisateur != null && password_verify($mdp, $utilisateur->getMdp())){
            //Génération de la vue
            $template = $this->getTwig()->load('index.html.twig');
            echo $template->render(array());
        }else{
            //Génération de la vue
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array());
            
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
        //supprimer les espaces
        
        $id = str_replace(' ', '', $id);
        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        if ( strlen($pseudo) <= 50 && strlen($id) <= 20 && strlen($mail) <= 320 && strlen($mdp) <= 100 && strlen($vmdp) <= 100 && strlen($nom) <= 50){ 
            // verifier si l'id n'est pas déjà utilisé
            if ( $managerutilisateur->find($id) == null){
                // verifier si le mail est valide 
                if (filter_var($mail, FILTER_VALIDATE_EMAIL)) {
                    // verifier si le mail n'est pas déjà utilisé
                    if ($managerutilisateur->findByMail($mail) == null){
                        // verifier si les deux mots de passe sont identiques
                        if ($mdp == $vmdp){  
                            //cripter le mot de passe
                            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
                        

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
        echo $template->render(array());          
    }

    /**
     * @brief afficher la page ou l'utilisateur rensegner son adre mail pour changer le mot de passe
     * 
     * @return void
     */
    public function afficherpageMDPOulier() : void {

        $template = $this->getTwig()->load('motDePasseOublie.html.twig');
        echo $template->render(array());  
        
    }
    /**
     * @brief envoie un mail à l'utilisateur pour qu'il puisse changer son mot de passe
     * @todo cryptage de id dans le lien du mail
     * @return void
     */
    public function envoieMailMDPOublie() : void {
        $mail = isset($_POST['email']) ? $_POST['email'] : null;
        $mail = str_replace(' ', '', $mail);
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->findByMail($mail);
        if ($utilisateur != null){
            // crypter le id
            $id = $utilisateur->getId();
            $message = "Bonjour, \n\n Vous avez demandé à réinitialiser votre mot de passe. Voici votre mot de passe : ".URL_SITE."index.php?controller=utilisateur&methode=afficherchangerMDP&id=".$id." \n\n Cordialement, \n\n L'équipe de la plateforme de vhs";
            mail($mail, "Réinitialisation de votre mot de passe", $message);
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array());
        }else{
            //gener un popoup pour dire que le mail n'est pas valide

            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array());  
        }
    }

    /**
     * @brief affiche la page ou l'utilisateur peut changer son mot de passe
     * @todo decryptage de id dans get
     * @return void
     */
    public function afficherchangerMDP() : void {
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        if ($id != null) {
            
        
            $utilisateur = $managerutilisateur->find($id);
            if ($utilisateur != null){
                

                $template = $this->getTwig()->load('changerMDP.html.twig');
                echo $template->render(array('id' => $id));

            }
        }
    }

    /**
     * @brief permet de changer le mot de passe de l'utilisateur et met à jour la base de données
     * 
     * @return void
     */
    public function changerMDP() : void {
        
        $id = isset($_POST['id']) ? $_POST['id'] : null;
    
        $mdp = isset($_POST['mdp']) ? $_POST['mdp'] : null;
        $vmdp = isset($_POST['vmdp']) ? $_POST['vmdp'] : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->find($id);
        if ($utilisateur != null){
            if ( strlen($mdp) <= 100 && strlen($vmdp) <= 100){
               
            
                if ($mdp == $vmdp){
                    //cripter le mot de passe a faire
                    $mdp = password_hash($mdp, PASSWORD_DEFAULT);
                    $utilisateur->setMdp($mdp);
                    $managerutilisateur->update($utilisateur);
                    $template = $this->getTwig()->load('connection.html.twig');
                    echo $template->render(array());
                }else{
                    $template = $this->getTwig()->load('changerMDP.html.twig');
                    echo $template->render(array('id' => $id));
                }
            }
        }
    }


    public function show() : void {
        // Récupère id_utlisateur dans $_GET et affiche la page du profil de l'utilisateur
    }
        



    
}