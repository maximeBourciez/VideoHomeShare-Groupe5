<?php


class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }


    /**
     * @bref permet d'afficher la page de connection
     *
     * @return void
     */
    public function connexion(): void
    {

        //Génération de la vue
        $template = $this->getTwig()->load('connection.html.twig');
        echo $template->render(array());
    }

    /**
     * @brief permet d'afficher la page d'inscription
     * 
     * @return void
     */
    public function inscription(): void
    {

        //Génération de la vue
        $template = $this->getTwig()->load('inscription.html.twig');
        echo $template->render(array());
    }

    /**
     * @brief vérifie les informations saisies lors de la connection et connecte l'utilisateur et revoit sur la page d'acceuil si les informations sont correctes  sinon renvoie sur la page de connection
     *
     * @return void
     */
    public function checkInfoConnecter()
    {

        $mail = isset($_POST['mail']) ?  htmlspecialchars($_POST['mail']) : null;
        $mdp = isset($_POST['pwd']) ?  htmlspecialchars($_POST['pwd']) : null;

        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $message = "";
        $veriflong=  Utilitaires::comprisEntre($mail, 320, 6, "le mail doit contenir", $message);
        if (Utilitaires::comprisEntre($mail, 320, 6, "le mail doit contenir", $message)) {
            $utilisateur = $managerutilisateur->findByMail($mail);

            if (Utilitaires::utilisateurExiste($utilisateur, $message ) && !Utilitaires::isBruteForce($utilisateur->getId(), $message ) && Utilitaires::motDePasseCorrect($mdp, $utilisateur->getMdp(), $utilisateur , $message)) {
                $utilisateur->setMdp(null);
                Utilitaires::resetBrutForce($utilisateur->getId());
                $_SESSION['utilisateur'] = serialize($utilisateur);
                $this->getTwig()->addGlobal('utilisateurConnecte', $_SESSION['utilisateur']);

                //Génération de la vue
                $this->show();
            }
            else{
                $template = $this->getTwig()->load('connection.html.twig');
                echo $template->render(array('messagederreur' => $message));
            }
        }
        else{
           
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('messagederreur' =>$message));
        }
    }
    /**
     * @brief vérifie les informations saisies lors de l'inscription et crée un utilisateur si les informations sont correctes et renvoie sur la page de connection   sinon renvoie sur la page d'inscription
     * 
     * @return void
     */
    public function checkInfoInscription()
    {

        //réccupération des données du formulaire
        $id = isset($_POST['idantifiant']) ? htmlspecialchars( $_POST['idantifiant']) : null;
        $pseudo = isset($_POST['pseudo']) ?  htmlspecialchars($_POST['pseudo']) : null;
        $mail = isset($_POST['mail']) ?  htmlspecialchars($_POST['mail']) : null;
        $date = isset($_POST['date']) ?  htmlspecialchars($_POST['date']) : null;
        $mdp = isset($_POST['mdp']) ?  htmlspecialchars($_POST['mdp']) : null;
        $vmdp = isset($_POST['vmdp']) ?  htmlspecialchars($_POST['vmdp']) : null;
        $nom = isset($_POST['nom']) ?  htmlspecialchars($_POST['nom']) : null;
        //supprimer les espaces

        $id = str_replace(' ', '', $id);
        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        // vérification que la personne est assez vieille



        $message = "";
        if (
            Utilitaires::comprisEntre($id, 20, 3, "L'identifiant doit contenir ", $message ) && Utilitaires::comprisEntre($pseudo, 50, 3, "Le pseudo doit contenir ", $message ) &&
            Utilitaires::comprisEntre($mail, 50, 3, "Le mail doit contenir ", $message ) && Utilitaires::comprisEntre($nom, 50, 3, "Le nom doit contenir ", $message ) &&
            Utilitaires::comprisEntre($mdp, null, 8, "Le mot de passe doit contenir ", "inscription") && Utilitaires::comprisEntre($vmdp, null, 8, "Le mot de passe de confirmation doit contenir ", "inscription")
            && Utilitaires::estRobuste($mdp, $message ) && Utilitaires::ageCorrect($date, 13,$message) && Utilitaires::mailCorrectExistePas($mail, $message,  $managerutilisateur) && Utilitaires::egale($mdp, $vmdp, "Les mots de passe", $message)
            && Utilitaires::idExistePas($id, $message, $managerutilisateur ) && !Utilitaires::verificationDeNom($id, "l'Identifiant ",$message ) && !Utilitaires::verificationDeNom($pseudo, "le pseudo",$message) && !Utilitaires::verificationDeNom($nom, "le nom",$message)
        ) {

            //cripter le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            //création de l'utilisateur
            $role = Role::Utilisateur;
            $newUtilisateur = new Utilisateur($id, $pseudo, $nom, $mail, $mdp, $role, "images/image_de_profil_de_base.svg", "images/Baniere_de_base.png");
            $managerutilisateur->create($newUtilisateur);
            //Génération de la vue
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Votre compte a bien été créé."));
        }else{
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => $message));
        }
    }

    /**
     * @brief afficher la page ou l'utilisateur rensegner son adre mail pour changer le mot de passe
     * 
     * @return void
     */
    public function afficherpageMDPOublier(): void
    {

        $template = $this->getTwig()->load('motDePasseOublie.html.twig');
        echo $template->render(array());
    }
    /**
     * @brief envoie un mail à l'utilisateur pour qu'il puisse changer son mot de passe
     * 
     * @return void
     */
    public function envoieMailMDPOublie(): void
    {
        $mail = isset($_POST['email']) ?  htmlspecialchars($_POST['email']) : null;
        $mail = str_replace(' ', '', $mail);
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->findByMail($mail);
        $messageErreur = "";
        if (Utilitaires::utilisateurExiste($utilisateur, $messageErreur)) {
            // crypter le id
            $id = $utilisateur->getId();
            $token = Utilitaires::generateToken($id, 6);

            $message = "Bonjour, \n\n Vous avez demandé à réinitialiser votre mot de passe.\n Voici votre lien pour changer de mot de passe : " . WEBSITE_LINK . "index.php?controller=utilisateur&methode=afficherchangerMDP&token=" . $token . " \n\n Cordialement, \n\n L'équipe de la plateforme de vhs";
            mail($mail, "Réinitialisation de votre mot de passe", $message);
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Un mail vous a été envoyé pour changer votre mot de passe"));
        }
        else{
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('message' => $messageErreur));
        }
    }

    /**
     * @brief affiche la page ou l'utilisateur peut changer son mot de passe
     * 
     * @return void
     */
    public function afficherchangerMDP(): void
    {
        $token = isset($_GET['token']) ?  htmlspecialchars($_GET['token']) : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $tokenUilisateur = Utilitaires::verifyToken($token);
        $messageErreur = "";
        if (Utilitaires::nonNull($tokenUilisateur,$messageErreur )) {
            $utilisateur = $managerutilisateur->find($tokenUilisateur['id']);
            if (Utilitaires::utilisateurExiste($utilisateur, $messageErreur)) {


                $template = $this->getTwig()->load('changerMDP.html.twig');
                echo $template->render(array('id' => $utilisateur->getId()));
            }else{
                $template = $this->getTwig()->load('inscription.html.twig');
                echo $template->render(array('messagederreur' => $messageErreur));
            }
        }else{
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => $messageErreur));
        }
    }

    /**
     * @brief permet de changer le mot de passe de l'utilisateur et met à jour la base de données
     * 
     * @return void
     */
    public function changerMDP(): void
    {
        // récupération des données du formulaire
        $id = isset($_POST['id']) ?  htmlspecialchars($_POST['id']) : null;

        $mdp = isset($_POST['mdp']) ?  htmlspecialchars($_POST['mdp']) : null;
        $vmdp = isset($_POST['vmdp']) ?  htmlspecialchars($_POST['vmdp']) : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->find($id);
        $messageErreur = "";
        if (
            Utilitaires::utilisateurExiste($utilisateur, $messageErreur) && Utilitaires::comprisEntre($mdp, null, 8, "Le mot de passe doit contenir ", $messageErreur) &&
            Utilitaires::comprisEntre($vmdp, null, 8, "Le mot de passe de confirmation doit contenir ", $messageErreur) &&
            Utilitaires::egale($mdp, $vmdp, "Les mots de passe", $messageErreur) && Utilitaires::estRobuste($mdp, $messageErreur)
        ) {
            //crypter le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $utilisateur->setMdp($mdp);

            // modifier le mot de passe dans la base de données
            $managerutilisateur->update($utilisateur);
            
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Votre mot de passe a bien été changé"));
        }else{
            $template = $this->getTwig()->load('changerMDP.html.twig');
            echo $template->render(array('id' => $id, 'messagederreur' => $messageErreur));
        }
    }

    /**
     *  @brief permet d'afficher la page d'un utilisateur 
     * @return void
     */
    public function show(): void
    {
        // Récupère id_utlisateur dans $_GET et affiche la page du profil de l'utilisateur
        $id = isset($_GET['id_utilisateur']) ?  htmlspecialchars($_GET['id_utilisateur']) : null;



        // recuper que si l'utilisateur est connecter
        if (isset($_SESSION['utilisateur'])) {
            //recuperer l'utilisateur connecter if
            $personneConnect = unserialize($_SESSION['utilisateur']);
            if ($id == null) {
                $id = $personneConnect->getId();
            }
        } else {
            $personneConnect = null;
        }

        // instanciation des managers de base de données
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $managermesage = new MessageDAO($this->getPdo());

        //récupération de l'utilisateur à partir de id dans l'url
        $utilisateur = $managerutilisateur->find($id);

        //récupération des messages de l'utilisateur
        $messages = $managermesage->listerMessagesParIdUser($id);
        $messageErreur = "";
        if (Utilitaires::utilisateurExiste($utilisateur, $messageErreur)) {
            $template = $this->getTwig()->load('profilUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'messages' => $messages, 'utilisateurConnecter' => $personneConnect));
        }else{
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => $messageErreur));
        }
    }

    /**
     * @brief permet de modifier les informations de l'utilisateur
     */
    public function edit(): void
    {
        if (isset($_SESSION['utilisateur'])) {
            //recuperer l'utilisateur connecter if
            $utilisateur = unserialize($_SESSION['utilisateur']);
        } else {
            $utilisateur = null;
        }
        $messageErreur = "";
        if (Utilitaires::utilisateurExiste($utilisateur, $messageErreur)) {
            //Génération de la vue
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur));
        }
        else{
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => $messageErreur));
        }
    }



    /**
     * @brief permet de verifier modifier les informations de l'utilisateur dans la base de données et renvoie sur la page de edition
     */
    public function modificationprofil(): void
    {
        // récupération des données du formulaire
        $id = isset($_POST['id']) ?  htmlspecialchars($_POST['id']) : null;
        $pseudo = isset($_POST['pseudo']) ?  htmlspecialchars($_POST['pseudo']) : null;
        $nom = isset($_POST['nom']) ?  htmlspecialchars($_POST['nom']) : null;


        $utilisateur = unserialize($_SESSION['utilisateur']);

        //supprimer les espaces
        $id = str_replace(' ', '', $id);
        $pseudo = str_replace(' ', '', $pseudo);


        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $messageErreur = "";
        if (
            Utilitaires::comprisEntre($id, 20, 3, "L'identifiant doit contenir ", $messageErreur ) && Utilitaires::comprisEntre($pseudo, 50, 3, "Le pseudo doit contenir ", $messageErreur) &&
            Utilitaires::comprisEntre($nom, 50, 3, "Le nom doit contenir ", $messageErreur)  && Utilitaires::fichierTropLourd($_FILES['urlImageProfil'], "profil", $messageErreur) &&
            Utilitaires::fichierTropLourd($_FILES['urlImageBaniere'], "baniere", $messageErreur)&& !Utilitaires::verificationDeNom($id, "l'idantifiant", $messageErreur) && !Utilitaires::verificationDeNom($pseudo, "le pseudo", $messageErreur) && !Utilitaires::verificationDeNom($nom, "le nom", $messageErreur)
        ) {
            // verifier si l'id n'est pas déjà utilisé
            if ($id == $utilisateur->getId() || Utilitaires::idExistePas($id, $messageErreur, $utilisateur)) {
                //création de l'utilisateur

                $utilisateur->setId($id);
                $utilisateur->setPseudo($pseudo);
                $utilisateur->setNom($nom);
                // récupérer les fichiers images  de profil
                if (isset(($_FILES['urlImageProfil']))) {

                    if ($_FILES['urlImageProfil']['name'] != '') {
                        //supprimer l'ancienne image
                        Utilitaires::ajourfichier($_FILES['urlImageProfil'], "Profil" ,$messageErreur, $utilisateur);
                    }
                }
                if (isset(($_FILES['urlImageBaniere']))) {
                    Utilitaires::ajourfichier($_FILES['urlImageBaniere'], "Baniere",$messageErreur, $utilisateur);
                }
                // mettre à jour l'utilisateur dans la base de données
                $utilisateur->setMdp($managerutilisateur->find($utilisateur->getId())->getMdp());
                $managerutilisateur->update($utilisateur);
                $utilisateur->setMdp(null);
                $_SESSION['utilisateur'] = serialize($utilisateur);
                //Génération de la vue
                $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
                echo $template->render(array('utilisateur' => $utilisateur, 'message' => "Vos informations ont bien été modifiées"));
                return;
            }else{
                $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
                echo $template->render(array('utilisateur' => $utilisateur, 'messagederreur' => $messageErreur));
            }
        }else{
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'messagederreur' => $messageErreur));
        }
    }

    /**
     * @brief permet de changer le mail de l'utilisateur
     */
    public function changerMail(): void
    {
        // récupération des données du formulaire
        $mail = isset($_POST['mail']) ?  htmlspecialchars($_POST['mail']) : null;
        $mailconf = isset($_POST['mailconf']) ?  htmlspecialchars($_POST['mailconf']) : null;

        //supprimer les espaces
        $mail = str_replace(' ', '', $mail);
        $mailconf = str_replace(' ', '', $mailconf);
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $messageErreur = "";
        // vérifier si les deux mails sont identiques
        if (
            Utilitaires::comprisEntre($mail, 320, 6, "Le mail doit contenir ", $messageErreur) && Utilitaires::comprisEntre($mail, 320, 6, "Le mail de verification doit contenir ", $messageErreur) &&
            Utilitaires::egale($mail, $mailconf, "Les mails ", $messageErreur) && Utilitaires::mailCorrectExistePas($mail, $messageErreur, $managerutilisateur)
        ) {
            // mettre à jour le mail de l'utilisateur
            $utilisateur->setMail($mail);
            $utilisateur->setMdp($managerutilisateur->find($utilisateur->getId())->getMdp());
            var_dump($utilisateur);
            $managerutilisateur->update($utilisateur);
            $utilisateur->setMdp(null);
            $_SESSION['utilisateur'] = serialize($utilisateur);
            //Génération de la vue
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur));
        }else{
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'messagederreur' => $messageErreur));
        }
       
    }
    /**
     * @bref affiche le notification de l'utilisateur
     * @return void
     */
    public function notification(): void
    {

        $template = $this->getTwig()->load('enConstruction.htlm.twig');
        echo $template->render(array('fontionliter' => "notification"));
    }
    /**
     * @bref permet de deconnecter l'utilisateur
     * @return void
     */
    public function deconnexion(): void
    {
        session_destroy();
        $this->getTwig()->addGlobal('utilisateurConnecte', null);
        $template = $this->getTwig()->load('connection.html.twig');
        echo $template->render(array('message' => "Vous avez bien été déconnecté"));
    }
  

}
