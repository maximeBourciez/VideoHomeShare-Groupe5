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

        $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
        $mdp = isset($_POST['pwd']) ? $_POST['pwd'] : null;

        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        if ($this->comprisEntre($mail, 320, 6, "Le mail doit contenir", "connection")) {
            $utilisateur = $managerutilisateur->findByMail($mail);

            if ($this->utilisateurExiste($utilisateur, "inscription") && !$this->isBruteForce($utilisateur->getId()) && $this->motDePasseCorrect($mdp, $utilisateur->getMdp(), $utilisateur)) {
                $utilisateur->setMdp(null);
                $this->resetAttempts($utilisateur->getId());
                $_SESSION['utilisateur'] = serialize($utilisateur);
                $this->getTwig()->addGlobal('utilisateurConnecte', $_SESSION['utilisateur']);

                //Génération de la vue
                $this->show();
            }
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
        $id = isset($_POST['idantifiant']) ? $_POST['idantifiant'] : null;
        $pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : null;
        $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
        $date = isset($_POST['date']) ? $_POST['date'] : null;
        $mdp = isset($_POST['mdp']) ? $_POST['mdp'] : null;
        $vmdp = isset($_POST['vmdp']) ? $_POST['vmdp'] : null;
        $nom = isset($_POST['nom']) ? $_POST['nom'] : null;
        //supprimer les espaces

        $id = str_replace(' ', '', $id);
        $mail = str_replace(' ', '', $mail);

        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        // vérification que la personne est assez vieille




        if (
            $this->comprisEntre($id, 20, 3, "L'identifiant doit contenir ", "inscription") && $this->comprisEntre($pseudo, 50, 3, "Le pseudo doit contenir ", "inscription") &&
            $this->comprisEntre($mail, 50, 3, "Le mail doit contenir ", "inscription") && $this->comprisEntre($nom, 50, 3, "Le nom doit contenir ", "inscription") &&
            $this->comprisEntre($mdp, null, 8, "Le mot de passe doit contenir ", "inscription") && $this->comprisEntre($vmdp, null, 8, "Le mot de passe de confirmation doit contenir ", "inscription")
            && $this->estRobuste($mdp, "inscription") && $this->ageCorrect($date, 13) && $this->mailCorrectExistePas($mail, "inscription") && $this->egale($mdp, $vmdp, array('messagederreur' => "Les mots de passe ne sont pas identiques"), "inscription")
            && $this->idExistePas($id, "inscription") && !$this->verificationDeNom($id, "inscription") && !$this->verificationDeNom($pseudo, "inscription") && !$this->verificationDeNom($nom, "inscription")
        ) {

            //cripter le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            //création de l'utilisateur
            $newUtilisateur = new Utilisateur($id, $pseudo, $nom, $mail, $mdp, "Utilisateur", "images/image_de_profil_de_base.svg", "images/Baniere_de_base.png");
            $managerutilisateur->create($newUtilisateur);
            //Génération de la vue
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Votre compte a bien été créé."));
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
        $mail = isset($_POST['email']) ? $_POST['email'] : null;
        $mail = str_replace(' ', '', $mail);
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->findByMail($mail);
        if ($this->utilisateurExiste($utilisateur, "inscription")) {
            // crypter le id
            $id = $utilisateur->getId();
            $token = $this->generateToken($id, 6);

            $message = "Bonjour, \n\n Vous avez demandé à réinitialiser votre mot de passe.\n Voici votre lien pour changer de mot de passe : " . WEBSITE_LINK . "index.php?controller=utilisateur&methode=afficherchangerMDP&token=" . $token . " \n\n Cordialement, \n\n L'équipe de la plateforme de vhs";
            mail($mail, "Réinitialisation de votre mot de passe", $message);
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Un mail vous a été envoyé pour changer votre mot de passe"));
        }
    }

    /**
     * @brief affiche la page ou l'utilisateur peut changer son mot de passe
     * 
     * @return void
     */
    public function afficherchangerMDP(): void
    {
        $token = isset($_GET['token']) ? $_GET['token'] : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $tokenUilisateur = $this->verifyToken($token);
        if ($this->nonNull($tokenUilisateur, "Ce lien n'est pas valide ", "motDePasseOublie")) {
            $utilisateur = $managerutilisateur->find($tokenUilisateur['id']);
            if ($this->utilisateurExiste($utilisateur, "motDePasseOublie")) {


                $template = $this->getTwig()->load('changerMDP.html.twig');
                echo $template->render(array('id' => $utilisateur->getId()));
            }
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
        $id = isset($_POST['id']) ? $_POST['id'] : null;

        $mdp = isset($_POST['mdp']) ? $_POST['mdp'] : null;
        $vmdp = isset($_POST['vmdp']) ? $_POST['vmdp'] : null;
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerutilisateur->find($id);
        if (
            $this->utilisateurExiste($utilisateur, "motDePasseOublie") && $this->comprisEntre($mdp, null, 8, "Le mot de passe doit contenir ", "changerMDP") &&
            $this->comprisEntre($vmdp, null, 8, "Le mot de passe de confirmation doit contenir ", "changerMDP") &&
            $this->egale($mdp, $vmdp, array('messagederreur' => "Les mots de passe ne sont pas identiques", 'id' => $id), "changerMDP") && $this->estRobuste($mdp, "changerMDP")
        ) {
            //crypter le mot de passe
            $mdp = password_hash($mdp, PASSWORD_DEFAULT);
            $utilisateur->setMdp($mdp);

            // modifier le mot de passe dans la base de données
            $managerutilisateur->update($utilisateur);
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array('message' => "Votre mot de passe a bien été changé"));
        }
    }

    /**
     *  @brief permet d'afficher la page d'un utilisateur 
     * @return void
     */
    public function show(): void
    {
        // Récupère id_utlisateur dans $_GET et affiche la page du profil de l'utilisateur
        $id = isset($_GET['id_utilisateur']) ? $_GET['id_utilisateur'] : null;



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

        if ($this->utilisateurExiste($utilisateur, "inscription")) {
            $template = $this->getTwig()->load('profilUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur, 'messages' => $messages, 'utilisateurConnecter' => $personneConnect));
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

        if ($this->utilisateurExiste($utilisateur, "inscription")) {
            //Génération de la vue
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('utilisateur' => $utilisateur));
        }
    }



    /**
     * @brief permet de verifier modifier les informations de l'utilisateur dans la base de données et renvoie sur la page de edition
     */
    public function modificationprofil(): void
    {
        // récupération des données du formulaire
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : null;
        $nom = isset($_POST['nom']) ? $_POST['nom'] : null;


        $utilisateur = unserialize($_SESSION['utilisateur']);

        //supprimer les espaces
        $id = str_replace(' ', '', $id);
        $pseudo = str_replace(' ', '', $pseudo);


        $managerutilisateur = new UtilisateurDAO($this->getPdo());

        if (
            $this->comprisEntre($id, 20, 3, "L'identifiant doit contenir ", "modifierUtilisateur", $utilisateur) && $this->comprisEntre($pseudo, 50, 3, "Le pseudo doit contenir ", "modifierUtilisateur", $utilisateur) &&
            $this->comprisEntre($nom, 50, 3, "Le nom doit contenir ", "modifierUtilisateur", $utilisateur)  && $this->fichierTropLourd($_FILES['urlImageProfil'], "profil", $utilisateur) &&
            $this->fichierTropLourd($_FILES['urlImageBaniere'], "baniere", $utilisateur)&& !$this->verificationDeNom($id, "modifierUtilisateur") && !$this->verificationDeNom($pseudo, "modifierUtilisateur") && !$this->verificationDeNom($nom, "modifierUtilisateur")
        ) {
            // verifier si l'id n'est pas déjà utilisé
            if ($id == $utilisateur->getId() || $this->idExistePas($id, "modifierUtilisateur", $utilisateur)) {
                //création de l'utilisateur

                $utilisateur->setId($id);
                $utilisateur->setPseudo($pseudo);
                $utilisateur->setNom($nom);
                // récupérer les fichiers images  de profil
                if (isset(($_FILES['urlImageProfil']))) {

                    if ($_FILES['urlImageProfil']['name'] != '') {
                        //supprimer l'ancienne image
                        $this->ajourfichier($_FILES['urlImageProfil'], "Profil", $utilisateur);
                    }
                }
                if (isset(($_FILES['urlImageBaniere']))) {
                    $this->ajourfichier($_FILES['urlImageBaniere'], "Baniere", $utilisateur);
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
            }
        }
    }

    /**
     * @brief permet de changer le mail de l'utilisateur
     */
    public function changerMail(): void
    {
        // récupération des données du formulaire
        $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
        $mailconf = isset($_POST['mailconf']) ? $_POST['mailconf'] : null;

        //supprimer les espaces
        $mail = str_replace(' ', '', $mail);
        $mailconf = str_replace(' ', '', $mailconf);
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        // vérifier si les deux mails sont identiques
        if (
            $this->comprisEntre($mail, 320, 6, "Le mail doit contenir ", "modifierUtilisateur", $utilisateur) && $this->comprisEntre($mail, 320, 6, "Le mail de verification doit contenir ", "modifierUtilisateur", $utilisateur) &&
            $this->egale($mail, $mailconf, array('messagederreur' => "Les mails ne sont pas identiques"), "modifierUtilisateur") && $this->mailCorrectExistePas($mail, "modifierUtilisateur", $utilisateur)
        ) {
            // mettre à jour le mail de l'utilisateur
            $utilisateur->setMail($mail);
            $utilisateur->setMdp($managerutilisateur->find($utilisateur->getId())->getMdp());
            var_dump($utilisateur);
            $managerutilisateur->update($utilisateur);
            $utilisateur->setMdp(null);
            $_SESSION['utilisateur'] = serialize($utilisateur);
        }
        //Génération de la vue
        $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
        echo $template->render(array('utilisateur' => $utilisateur));
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
     * @bref pemet de verifier si la valeur est compris entre deux valeurs
     * @param string $val la valeur que l'on veut vérifier
     * @param int $valmax la valeur maximal que l'on autorise
     * @param int $valmin la valeur minimal que l'on autorise
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param string $page la page sur laquelle on veut renvoyer un message d'erreur
     * @return bool
     * 
     */
    public function comprisEntre(string $val, ?int $valmax, int $valmin, string $messageErreur, string $page, $utilisateur = null): bool
    {
        $valretour = true;
        if (strlen($val) <= $valmin) {

            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('messagederreur' => $messageErreur . " au moins " . $valmin . " caractères", 'utilisateur' => $utilisateur));
            $valretour = false;
        }
        if (strlen($val) >= $valmax and $valmax != null) {

            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('messagederreur' => $messageErreur . " au maximum " . $valmax . " caractères", 'utilisateur' => $utilisateur));
            $valretour = false;
        }

        return $valretour;
    }

    /**
     * @bref permet de verifier si les deux valeurs sont identiques
     * @param string $val1 la première valeur
     * @param string $val2 la deuxième valeur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool
     * 
     */
    public function egale(string $val1, string $val2, array $messageErreur, string $page): bool
    {
        $valretour = true;
        if ($val1 != $val2) {
            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render($messageErreur);
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @bref permet de verifier si le mail est correct  
     * @param string $mail le mail que l'on veut vérifier
     * @return bool
     * 
     */
    public function mailCorrectExistePas(string $mail, string $page, $utilisateur = null): bool
    {
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $valretour = true;

        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || $managerutilisateur->findByMail($mail) != null) {
            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('messagederreur' => "Le mail n'est pas valide", 'utilisateur' => $utilisateur));
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @bref permet de verifier si l'utilisateur est assez vieux
     * @param string $date la date de naissance de l'utilisateur
     * @param int $age l'age minimal de l'utilisateur
     * @return bool
     * 
     */
    public function ageCorrect(string $date, int $age): bool
    {
        $valretour = true;
        if (strtotime($date) >= strtotime(date("Y-m-d") . " -" . ($age * 12) . " month")) {
            $template = $this->getTwig()->load('inscription.html.twig');
            echo $template->render(array('messagederreur' => "Vous êtes trop jeune pour vous inscrire"));
            $valretour = false;
        }
        return $valretour;
    }
    /**
     * @bref génére un token 
     * @param string $userId le id de l'utilisateur que l'on veut mettre dans le token
     * @param int $temp la durée de vie du token en heure
     * @return string  le token
     */
    function generateToken(?string $userId, ?int $temp = 1)
    {
        $secretKey = SECRET_KEY;
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + 3600 * $temp,
            'id' => $userId,
        ]);

        $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secretKey, true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * @bref permet de renvoyer les informations du token id utilisateur et date d'expiration
     * @param string $token le token que l'on veut connaitre les informations
     * @return array
     */
    function verifyToken(?string $token): ?array
    {
        $secretKey = SECRET_KEY;
        if ($token == null) {
            return null;
        }
        list($header, $payload, $signature) = explode('.', $token);

        $validSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', $header . "." . $payload, $secretKey, true)), '+/', '-_'), '=');

        if ($validSignature !== $signature) {
            return null;
        }

        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);

        if ($data['exp'] < time()) {
            return null;
        }

        return $data;
    }
    /**
     * @bref permet de verifier si l'utilisateur existe et ranvoie un message d'erreur si il n'existe pas sur la page donnée
     * @param Utilisateur $useur l'utilisateur que l'on veut vérifier si il existe
     * @param string $page la page sur laquelle on veut renvoyer un message d'erreur
     * @return bool
     */
    public function utilisateurExiste(?Utilisateur $useur, string $page): bool
    {
        $valretour = true;
        if ($useur == null) {
            $valretour = false;
            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('message' => " Ce compte n'existe pas veuillez vous inscrire"));
        }
        return $valretour;
    }
    /**
     * @bref permet de verifier si le mot de passe est correct et renvoie un message d'erreur si il ne l'est pas
     * @param string $mdp le mot de passe que l'on veut vérifier
     * @param string $mdpBDD le mot de passe de la base de données (correct)
     * @return bool
     */
    public function motDePasseCorrect(string $mdp, string $mdpBDD, $utilisateur): bool
    {
        $valretour = true;
        if (!password_verify($mdp, $mdpBDD)) {
            $message = "";
            $valretour = false;
            $this->recordFailedAttempt($utilisateur->getId());
            if (isset($_SESSION['login_attempts'][$utilisateur->getId()]['count'])) {
                if ($_SESSION['login_attempts'][$utilisateur->getId()]['count'] == 5) {
                    $message = "Vous avez été bloqué pour 10 minutes";
                } else {
                    $message = "il vous reste " . (5 - $_SESSION['login_attempts'][$utilisateur->getId()]['count']) . " tentatives avant d'être bloqué 10 minutes";
                }
            }
            $template = $this->getTwig()->load('connection.html.twig');
            echo $template->render(array("messagederreur" => "Le mot de passe est incorrect $message"));
        }
        return $valretour;
    }
    /**
     * @bref permet de verifier si le mot de passe est robuste et renvoie un message d'erreur si il ne l'est pas
     * @param string $mdp le mot de passe que l'on veut vérifier
     * @param string $page la page sur laquelle on veut renvoyer un message d'erreur
     * @return bool
     */
    public function estRobuste(string $mdp, string $page): bool
    {
        $valretour = true;
        $messageerreur = "Le mot de passe doit contenir";
        if (!preg_match('/[a-z]/', $mdp)) {
            $valretour = false;

            $messageerreur = $messageerreur + " au moins une minuscule";
        }
        if (!preg_match('/[A-Z]/', $mdp)) {
            $valretour = false;

            $messageerreur = ($messageerreur == "Le mot de passe doit contenir") ? $messageerreur + " au moins une majuscule" : $messageerreur + ", au moins une majuscule";
        }
        if (!preg_match('/[\d]/', $mdp)) {
            $valretour = false;

            $messageerreur = ($messageerreur == "Le mot de passe doit contenir") ? $messageerreur + " au moins un chiffre" : $messageerreur + ", au moins un chiffre";
        }
        if (!preg_match('/[@$!%*?&]/', $mdp)) {
            $valretour = false;

            $messageerreur = ($messageerreur == "Le mot de passe doit contenir") ? $messageerreur + " au moins un caractère spécial" : $messageerreur + " et au moins un caractère spécial";
        }
        if ($valretour == false) {
            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('messagederreur' => "$messageerreur"));
        }
        return $valretour;
    }
    /**
     * @bref permet de verifier si la valeur est non null et renvoie un message d'erreur si elle l'est
     * @param mixed $val
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param string $page la page sur laquelle on veut renvoyer un message d'erreur
     * @return bool
     */
    public function nonNull($val, string $messageErreur, string $page)
    {
        $valretour = true;
        if ($val == null) {
            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('messagederreur' => $messageErreur));
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @bref permet de  verifier l'identifiant de l'utilisateur  n'existe pas déjà
     * @param string $id l'identifiant que l'on veut vérifier
     * 
     */
    public function idExistePas(string $id, string $page, $utilisateur = null): bool
    {
        $managerutilisateur = new UtilisateurDAO($this->getPdo());
        $valretour = true;
        if ($managerutilisateur->find($id) != null) {
            $template = $this->getTwig()->load("$page.html.twig");
            echo $template->render(array('messagederreur' => "L'identifiant est déjà utilisé", 'utilisateur' => $utilisateur));
            $valretour = false;
        }
        return $valretour;
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
    /**
     * @bref permet de verifier si le fichier est trop lourd et renvoie un message d'erreur si il l'est
     * @param array $fichier le fichier que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param Utilisateur $utilisateur l'utilisateur sur lequel on veut renvoyer un message d'erreur
     */
    public function fichierTropLourd(array $fichier, ?string $messageErreur, ?Utilisateur $utilisateur = null): bool
    {
        $valretour = true;
        if ($fichier['size'] > 2000000) {
            $valretour = false;
            $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
            echo $template->render(array('messagederreur' => "Le fichier de $messageErreur est trop lourd", 'utilisateur' => $utilisateur));
        }
        return $valretour;
    }

    /**
     * @bref permet de mettre à jour l'image de l'utilisateur
     * @param array $fichier le fichier que l'on veut mettre à jour
     * @param string $type le type de fichier que l'on veut mettre à jour
     * @param Utilisateur $utilisateur l'utilisateur sur lequel on veut mettre à jour l'image
     */
    public function ajourfichier($fichier, $type, $utilisateur = null)
    {
        if ($fichier['name'] != '') {
            //supprimer l'ancienne image
            if (file_exists($utilisateur->getUrlImageBaniere()) && $utilisateur->getUrlImageProfil() != "images/" . $type . "_de_base.png") {
                switch ($type) {
                    case "Profil":
                        unlink($utilisateur->getUrlImageProfil());
                        break;
                    case "Baniere":
                        unlink($utilisateur->getUrlImageBaniere());
                        break;
                }
            }

            // donne le bon nom à l'image
            $fichier['name'] = "images/image" . $type . "_" . $utilisateur->getId() . "." . pathinfo($fichier['name'], PATHINFO_EXTENSION);
            //telecharger l'image
            if (move_uploaded_file($fichier["tmp_name"], $fichier['name'])) {
                // mettre à jour l'url de l'image dans l'objet utilisateur
                switch ($type) {
                    case "Profil":
                        $utilisateur->setUrlImageProfil($fichier['name']);
                        break;
                    case "Baniere":
                        $utilisateur->setUrlImageBaniere($fichier['name']);
                        break;
                }
            } else {

                $template = $this->getTwig()->load('modifierUtilisateur.html.twig');
                echo $template->render(array('messagederreur' => "Nous n'avons pas pu télécharger l'image de $type", 'utilisateur' => $utilisateur));
            }
        }
    }

    function isBruteForce($username)
    {
        $maxAttempts = 5;
        $lockoutTime = 10 * 60; // 10 minutes

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }

        if (!isset($_SESSION['login_attempts'][$username])) {
            $_SESSION['login_attempts'][$username] = ['count' => 0, 'last_attempt' => time()];
        }

        $attempts = $_SESSION['login_attempts'][$username];

        if ($attempts['count'] >= $maxAttempts && (time() - $attempts['last_attempt']) < $lockoutTime) {
            $template = $this->getTwig()->load('connection.html.twig');
            $tempsenminute = round(($lockoutTime - (time() - $attempts['last_attempt'])) / 60);
            $tempsenseconde = round(($lockoutTime - (time() - $attempts['last_attempt'])) % 60);
            $message = "Vous avez été bloqué pour  $tempsenminute  minutes $tempsenseconde secondes";
            echo $template->render(array('messagederreur' => $message));

            return true;
        }

        return false;
    }

    function recordFailedAttempt($username)
    {
        if (!isset($_SESSION['login_attempts'][$username])) {
            $_SESSION['login_attempts'][$username] = ['count' => 0, 'last_attempt' => time()];
        }

        $_SESSION['login_attempts'][$username]['count']++;
        $_SESSION['login_attempts'][$username]['last_attempt'] = time();

    }

    function resetAttempts($username)
    {
        if (isset($_SESSION['login_attempts'][$username])) {
            unset($_SESSION['login_attempts'][$username]);
        }
    }

    

    // Fonction pour vérifier si un texte contient des variantes de profanité avec des chiffres
    public function verificationDeNom($text, $page) : bool {

        $profanity_list = json_decode(file_get_contents("config/nomincorect.json"), true); // Vous pouvez ajouter d'autres mots
        // On parcourt chaque mot de profanité
        foreach ($profanity_list['nom'] as $word) {
            // Expression régulière pour détecter le mot de profanité avec des chiffres ou autres caractères spéciaux
            $pattern = '/' . preg_quote($word, '/') . '[0-9]*/i';  
            if (preg_match($pattern, $text)) {
                $template = $this->getTwig()->load("$page.html.twig");
                echo $template->render(array('messagederreur' => "Le texte contient de la profanité"));
                return true; // Le texte contient de la profanité
            }
        }
        return false; // Aucun mot de profanité trouvé
    }

}
