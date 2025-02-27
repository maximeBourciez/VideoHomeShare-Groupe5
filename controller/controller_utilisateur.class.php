<?php


class ControllerUtilisateur extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }


    /**
     * @brief permet d'afficher la page de connection
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
        // récupération des données du formulaire
        $mail = isset($_POST['mail']) ?  htmlspecialchars($_POST['mail']) : null;
        $mdp = isset($_POST['pwd']) ?  htmlspecialchars($_POST['pwd']) : null;
        // supprimer les espaces
        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $managerBannissement = new BannissementDAO($this->getPdo());
        
        $message = "";
        // vérification des informations saisies
        $verficationTailleMail = Utilitaires::comprisEntre($mail, 320, 6, "le mail doit contenir", $message);
        if ($verficationTailleMail) {
            $utilisateur = $managerutilisateur->findByMail($mail);
            // vérification que l'utilisateur existe et que le mot de passe est correct
            $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $message);
            if ($verficationUtilisateurExiste) {
                $verficationBruteForce = Utilitaires::isBruteForce($utilisateur->getId(), $message);
                $verficationMotDePasse = Utilitaires::motDePasseCorrect($mdp, $utilisateur->getMdp(), $utilisateur, $message);
                $verficationUtiliateurverifier = Utilitaires::verifUtiliateurverifier($utilisateur->getId(), $message, $managerutilisateur);
                if ($verficationUtilisateurExiste && $verficationMotDePasse && $verficationUtiliateurverifier && !$verficationBruteForce) {

                    $utilisateur->setMdp(null);
                    Utilitaires::resetBrutForce($utilisateur->getId());
                    //création de la variable de session
                    $_SESSION['utilisateur'] = serialize($utilisateur);

                    //ajout de l'utilisateur connecté dans les variables globales de twig
                    $this->getTwig()->addGlobal('utilisateurConnecte', unserialize($_SESSION['utilisateur']));

                    //Génération de la vue
                    if (isset($_SESSION['redirect_after_login']) && !empty($_SESSION['redirect_after_login'])) {
                        $redirect_url = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']); // Nettoyer la variable de session
                        header("Location: " . $redirect_url);
                        exit();
                    } else {
                        // Si aucune URL de redirection, afficher le profil par défaut
                        header("Location: index.php?controller=utilisateur&methode=show");
                        exit();
                    }
                } else {
                    // affichage de la page de connection avec un message d'erreur
                    $template = $this->getTwig()->load('connection.html.twig');
                    echo $template->render(array('messagederreur' => $message));
                }
            } else {
                // affichage de la page de connection avec un message d'erreur
                $template = $this->getTwig()->load('inscription.html.twig');
                echo $template->render(array('messagederreur' => $message));
            }
        } else {
            // affichage de la page de connection avec un message d'erreur
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('messagederreur' => $message));
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
        $id = isset($_POST['idantifiant']) ? htmlspecialchars($_POST['idantifiant']) : null;
        $pseudo = isset($_POST['pseudo']) ?  htmlspecialchars($_POST['pseudo']) : null;
        $mail = isset($_POST['mail']) ?  htmlspecialchars($_POST['mail']) : null;
        $date = isset($_POST['date']) ?  htmlspecialchars($_POST['date']) : null;
        $mdp = isset($_POST['mdp']) ?  htmlspecialchars($_POST['mdp']) : null;
        $vmdp = isset($_POST['vmdp']) ?  htmlspecialchars($_POST['vmdp']) : null;
        $CGU = isset($_POST['conditions']) ?  htmlspecialchars($_POST['conditions']) : null;

        //supprimer les espaces

        $id = str_replace(' ', '', $id);
        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        $message = "";
        // vérification des informations saisies lors de l'inscription
        $verficationTailleMail = Utilitaires::comprisEntre($mail, 320, 6, "le mail doit contenir", $message);
        $verficationTailleId = Utilitaires::comprisEntre($id, 20, 3, "l'identifiant doit contenir", $message);

        $verficationTaillePseudo = Utilitaires::comprisEntre($pseudo, 50, 3, "le pseudo doit contenir", $message);

        $verficationTailleMdp = Utilitaires::comprisEntre($mdp, null, 8, "le mot de passe doit contenir", $message);
        $verficationTailleVmdp = Utilitaires::comprisEntre($vmdp, null, 8, "le mot de passe de confirmation doit contenir", $message);

        $verficationRobuste = Utilitaires::estRobuste($mdp, $message);
        var_dump($message);
        $verficationAge = Utilitaires::ageCorrect($date, 13, $message);
        $verficationMailExistePas = Utilitaires::mailCorrectExistePas($mail, $message, $managerutilisateur);
        $verficationEgale = Utilitaires::egale($mdp, $vmdp, "Les mots de passe", $message);
        $verficationIdExistePas = Utilitaires::idExistePas($id, $message, $managerutilisateur);
        $verficationProfaniteId = !Utilitaires::verificationDeNom($id, "l'Identifiant ", $message);
        $verficationProfanitePseudo = !Utilitaires::verificationDeNom($pseudo, "le pseudo", $message);

        $verficationCGURcocher = Utilitaires::verifiecasecocher($CGU, $message, " les conditions générales d'utilisation");

        if (
            $verficationTailleMail && $verficationTailleId && $verficationTaillePseudo  && $verficationTailleMdp && $verficationTailleVmdp &&
            $verficationRobuste && $verficationAge && $verficationMailExistePas && $verficationEgale && $verficationIdExistePas && $verficationProfaniteId && $verficationProfanitePseudo
             && $verficationCGURcocher
        ) {

            //cripter le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            //création de l'utilisateur
            $role = Role::Utilisateur;
            $token = Utilitaires::generateToken($id, 24);
            mail($mail, "Confirmation de votre compte", "Bonjour, \n\n Vous avez créé un compte sur notre plateforme. Pour confirmer votre compte, veuillez cliquer sur le lien suivant : " . WEBSITE_LINK . "index.php?controller=utilisateur&methode=confirmationCompte&token=" . $token . " \n\n Cordialement, \n\n L'équipe de la plateforme de vhs");
            $newUtilisateur = new Utilisateur($id, $pseudo,  $mail, $mdp, $role, "images/Profil_de_base.svg", "images/Banniere_de_base.png");
            $managerutilisateur->create($newUtilisateur);
            //Génération de la vue
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "un mail vous a été envoyé pour confirmer votre compte veuillez cliquer sur le lien dans le mail"));
        } else {
            // affichage de la page d'inscription avec un message d'erreur
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
        // affichage de la page de mot de passe oublié
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
        // récupération des données du formulaire
        $mail = isset($_POST['email']) ?  htmlspecialchars($_POST['email']) : null;
        // supprimer les espaces
        $mail = str_replace(' ', '', $mail);
        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        $utilisateur = $managerutilisateur->findByMail($mail);
        $messageErreur = "";
        // vérification de l'existence de l'utilisateur
        $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
        if ($verficationUtilisateurExiste) {

            $id = $utilisateur->getId();
            // crypter le id de l'utilisateur
            $token = Utilitaires::generateToken($id, 6);
            //envoie du mail
            $message = "Bonjour, \n\n Vous avez demandé à réinitialiser votre mot de passe.\n Voici votre lien pour changer de mot de passe : " . WEBSITE_LINK . "index.php?controller=utilisateur&methode=afficherchangerMDP&token=" . $token . " \n\n Cordialement, \n\n L'équipe de la plateforme de vhs";
            mail($mail, "Réinitialisation de votre mot de passe", $message);
            // affichage de la page de connection avec un message de confirmation
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Un mail vous a été envoyé pour changer votre mot de passe"));
        } else {
            // affichage de la page de mot de passe oublié avec un message d'erreur
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
        // récupération du token dans l'url
        $token = isset($_GET['token']) ?  htmlspecialchars($_GET['token']) : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $tokenUilisateur = Utilitaires::verifyToken($token);
        $messageErreur = "";
        // vérification de l'existence de l'utilisateur
        $verficationTokenNoNull = Utilitaires::nonNull($tokenUilisateur, $messageErreur);
        if ($verficationTokenNoNull) {
            $utilisateur = $managerutilisateur->find($tokenUilisateur['id']);
            // vérification de l'existence de l'utilisateur
            $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
            if ($verficationUtilisateurExiste) {

                // affichage de la page de changement de mot de passe
                $template = $this->getTwig()->load('changerMDP.html.twig');
                echo $template->render(array('id' => $utilisateur->getId()));
            } else {
                // affichage de la page d'inscription avec un message d'erreur
                $template = $this->getTwig()->load('inscription.html.twig');
                echo $template->render(array('messagederreur' => $messageErreur));
            }
        } else {
            // affichage de la page d'inscription avec un message d'erreur
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
        // vérification des informations saisies lors du changement de mot de passe
        $verficationTailleMdp = Utilitaires::comprisEntre($mdp, null, 8, "le mot de passe doit contenir", $messageErreur);
        $verficationTailleVmdp = Utilitaires::comprisEntre($vmdp, null, 8, "le mot de passe de confirmation doit contenir", $messageErreur);
        $verficationEgale = Utilitaires::egale($mdp, $vmdp, "Les mots de passe", $messageErreur);
        $verficationRobuste = Utilitaires::estRobuste($mdp, $messageErreur);
        $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
        if ($verficationTailleMdp && $verficationTailleVmdp && $verficationEgale && $verficationRobuste && $verficationUtilisateurExiste) {
            //crypter le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $utilisateur->setMdp($mdp);

            // modifier le mot de passe dans la base de données
            $managerutilisateur->update($utilisateur);
            // affichage de la page de connection avec un message de confirmation
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Votre mot de passe a bien été changé"));
        } else {
            // affichage de la page de changement de mot de passe avec un message d'erreur
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
            if ($id != null) {
                $personneConnect = null;
            } else {
                $this->connexion();
            }
        }

        // instanciation des managers de base de données
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $managermessage = new MessageDAO($this->getPdo());
        $managerquiz = new QuizzDAO($this->getPdo());

        //récupération de l'utilisateur à partir de id dans l'url
        $utilisateur = $managerutilisateur->find($id);

        //récupération des messages de l'utilisateur
        $messages = $managermessage->listerMessagesParIdUser($id);
        $quizs = $managerquiz->findAllByUser($id);

        $messageErreur = "";
        // vérification de l'existence de l'utilisateur
        $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
        if ($verficationUtilisateurExiste) {
            $template = $this->getTwig()->load('profilUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'messages' => $messages, 'quizs' => $quizs));
        } else {
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => $messageErreur));
        }
    }

    /**
     * @brief permet de modifier les informations de l'utilisateur
     */
    public function edit(): void
    {
        // vérifier si l'utilisateur est connecté
        if (isset($_SESSION['utilisateur'])) {
            //recuperer l'utilisateur connecter if
            $utilisateur = unserialize($_SESSION['utilisateur']);
        } else {
            $utilisateur = null;
        }
        $messageErreur = "";
        // vérifier si l'utilisateur existe
        $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
        if ($verficationUtilisateurExiste) {
            // affichage de la page de modification de l'utilisateur
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur));
        } else {
            // affichage de la page d'inscription avec un message d'erreur
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

        $pseudo = isset($_POST['pseudo']) ?  htmlspecialchars($_POST['pseudo']) : null;
        

        // récupérer l'utilisateur connecté
        $utilisateur = unserialize($_SESSION['utilisateur']);

        //supprimer les espaces

        $pseudo = str_replace(' ', '', $pseudo);


        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $messageErreur = "";
        // vérification des informations saisies lors de la modification
        $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
        $verficationTaillePseudo = Utilitaires::comprisEntre($pseudo, 50, 3, "le pseudo doit contenir", $messageErreur);
        $verficationfichierProfilTropLourd = Utilitaires::fichierTropLourd($_FILES['urlImageProfil'], "profil", $messageErreur);
        $verficationfichierBaniereTropLourd = Utilitaires::fichierTropLourd($_FILES['urlImageBanniere'], "banniere", $messageErreur);
        $verficationProfanitePseudo = !Utilitaires::verificationDeNom($pseudo, "le pseudo", $messageErreur);

        if (
            $verficationUtilisateurExiste  && $verficationTaillePseudo && $verficationfichierProfilTropLourd &&
            $verficationfichierBaniereTropLourd  && $verficationProfanitePseudo 
        ) {

            // mettre à jour les informations de l'utilisateur
            $utilisateur->setPseudo($pseudo);
            
            // récupérer les fichiers images  de profil
            if (isset(($_FILES['urlImageProfil']))) {

                if ($_FILES['urlImageProfil']['name'] != '') {
                    //supprimer l'ancienne image
                    Utilitaires::ajourfichier($_FILES['urlImageProfil'], "Profil", $messageErreur, $utilisateur);
                }
            }
            if (isset(($_FILES['urlImageBanniere']))) {
                if ($_FILES['urlImageBanniere']['name'] != '') {
                    Utilitaires::ajourfichier($_FILES['urlImageBanniere'], "Banniere", $messageErreur, $utilisateur);
                }
            }
            // mettre à jour l'utilisateur dans la base de données
            $utilisateur->setMdp($managerutilisateur->findByMail($utilisateur->getMail())->getMdp());

            $managerutilisateur->update($utilisateur);
            $utilisateur->setMdp(null);
            $_SESSION['utilisateur'] = serialize($utilisateur);
            $this->getTwig()->addGlobal('utilisateurConnecte', unserialize($_SESSION['utilisateur']));
            // affichage de la page de modification de l'utilisateur avec un message de confirmation
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'message' => "Vos informations ont bien été modifiées"));
            return;
        } else {
            // affichage de la page de modification de l'utilisateur avec un message d'erreur
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
        // vérifier si les deux mails sont identiques et si le mail est correct
        $verficationTailleMail = Utilitaires::comprisEntre($mail, 320, 6, "le mail doit contenir", $messageErreur);
        $verficationTailleMailConf = Utilitaires::comprisEntre($mailconf, 320, 6, "le mail de verification doit contenir", $messageErreur);
        $verficationEgale = Utilitaires::egale($mail, $mailconf, "Les mails", $messageErreur);
        $verficationMailExistePas = Utilitaires::mailCorrectExistePas($mail, $messageErreur, $managerutilisateur);
        if ($verficationTailleMail && $verficationTailleMailConf && $verficationEgale && $verficationMailExistePas) {
            // mettre à jour le mail de l'utilisateur
            $utilisateur->setMail($mail);
            $utilisateur->setMdp($managerutilisateur->find($utilisateur->getId())->getMdp());
            var_dump($utilisateur);
            $managerutilisateur->update($utilisateur);
            $utilisateur->setMdp(null);
            $_SESSION['utilisateur'] = serialize($utilisateur);
            // affichage de la page de modification de l'utilisateur
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur));
        } else {
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'messagederreur' => $messageErreur));
        }
    }
    /**
     * @brief affiche le notification de l'utilisateur
     * @return void
     */
    public function notification(): void
    {

        $template = $this->getTwig()->load('enConstruction.htlm.twig');
        echo $template->render(array('fontionliter' => "notification"));
    }
    /**
     * @brief permet de deconnecter l'utilisateur
     * @return void
     */
    public function deconnexion(): void
    {
        // détruire la session
        session_destroy();
        // supprimer l'utilisateur connecté des variables globales de twig
        $this->getTwig()->addGlobal('utilisateurConnecte', null);
        $template = $this->getTwig()->load('connection.html.twig');
        echo $template->render(array('message' => "Vous avez bien été déconnecté"));
    }

    /**
     * @brief permet de confirmer le compte de l'utilisateur
     * 
     * @return void
     */
    public function confirmationCompte(): void
    {
        // récupération du token dans l'url
        $token = isset($_GET['token']) ?  htmlspecialchars($_GET['token']) : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $tokenUilisateur = Utilitaires::verifyToken($token);
        $messageErreur = "";
        // vérification de l'existence de l'utilisateur
        $verficationTokenNoNull = Utilitaires::nonNull($tokenUilisateur, $messageErreur);
        if ($verficationTokenNoNull) {
            $utilisateur = $managerutilisateur->find($tokenUilisateur['id']);
            // vérification de l'existence de l'utilisateur
            $verficationUtilisateurExiste = Utilitaires::utilisateurExiste($utilisateur, $messageErreur);
            if ($verficationUtilisateurExiste) {
                $utilisateur->setRole(Role::Utilisateur);
                $utilisateur->setEstValider(true);
                $managerutilisateur->update($utilisateur);
                // affichage de la page de connection avec un message de confirmation
                $template = $this->getTwig()->load('connection.html.twig');
                echo $template->render(array('message' => "Votre compte a bien été confirmé"));
            } else {
                // affichage de la page d'inscription avec un message d'erreur
                $template = $this->getTwig()->load('inscription.html.twig');
                echo $template->render(array('messagederreur' => $messageErreur));
            }
        } else {
            // affichage de la page d'inscription avec un message d'erreur
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => $messageErreur));
        }
    }


    /**
     * @brief Méthode de suppression d'un copte utlisateur
     * 
     * @detail Envoie un mail de confirmation de suppression du compte et le supprime en BD
     * 
     * @return void
     */
    public function supprimerCompte(): void
    {
        // Récupérer l'id de l'utilisateur
        $idUtilisateur = isset($_SESSION['utilisateur']) ? unserialize($_SESSION['utilisateur'])->getId() : null;

        // Récupérer l'adresse mail de l'utilisateur
        $managerUtilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerUtilisateur->find($idUtilisateur);
        $destinataire = $utilisateur->getMail(); 
        $sujet = "Confirmation de suppression de votre compte";
        
        // Contenu du mail en HTML
        $contenuMail = "
            <html>
            <head>
                <title>Confirmation de suppression de votre compte</title>
            </head>
            <body style='font-family: Arial, sans-serif; color: #333; text-align: center;'>
                <h2 style='color: #d9534f;'>Suppression de votre compte</h2>
                <p>Bonjour,</p>
                <p>Nous vous confirmons que votre compte a bien été supprimé de notre plateforme.</p>
                <p>Si vous n'êtes pas à l'origine de cette demande, veuillez nous contacter immédiatement.</p>
                <p>Cordialement,</p>
                <p>L'équipe du site</p>
            </body>
            </html>
        ";
        
        // En-têtes pour l'email en HTML
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
        $headers .= "From: " . WEBSITE_MAIL . "\r\n";
        
        // Envoi du mail
        if (mail($destinataire, $sujet, $contenuMail, $headers)) {
            // Déconnecter l'utilisateur 
            $this->deconnexion();

            // Afficher l'accueil 
            header('Location: index.php?');
        } else {
            header('Location: index.php?controller=utilisateur&methode=show&id_utilisateur=' . $idUtilisateur);
        }
    }
}
