<?php
/**
 * @brief Classe utilitaires
 * 
 * Cette classe contient des méthodes utilitaires qui peuvent être utilisées dans plusieurs classes
 * 
 * @file utilitaires.class.php
 * 
 * @version 1.0
 * 
 * @date Création : 16/12/2024 - Mise à jour : 22/12/2024
 */

class Utilitaires
{    
    /**
     * @bref pemet de verifier si la valeur est compris entre deux valeurs
     * @param string $val la valeur que l'on veut vérifier
     * @param int $valmax la valeur maximal que l'on autorise
     * @param int $valmin la valeur minimal que l'on autorise
     * @param string $contenu le contenu sur lequel on veut renvoyer un message d'erreur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool
     * 
     */
    public static function comprisEntre(string $val, ?int $valmax, int $valmin, string $contenu , string &$messageErreur): bool
    {
        $valretour = true;
        // si la valeur est plus petite que la valeur minimal
        if (strlen($val) <= $valmin) {

            
            $messageErreur = $contenu . " au moins " . $valmin . " caractères" ;
            $valretour = false;
        }
        // si la valeur est plus grande que la valeur maximal
        if (strlen($val) >= $valmax and $valmax != null) {

            
            $messageErreur = $contenu . " au maximum " . $valmax . " caractères";
            $valretour = false;
        }
        
        return $valretour;
    }

    /**
     * @bref permet de verifier si les deux valeurs sont identiques
     * @param string $val1 la première valeur
     * @param string $val2 la deuxième valeur
     * @param string $contenu le nom du contenu sur lequel on veut renvoyer un message d'erreur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool
     * 
     */
    public static function egale(string $val1, string $val2, string $contenu , string &$messageErreur): bool
    {
        $valretour = true;
        // si les deux valeurs ne sont pas identiques
        if ($val1 != $val2) {
            
            $messageErreur = $contenu . "ne sont pas identiques";
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @bref permet de verifier si le mail est correct  
     * @param string $mail le mail que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param UtilisateurDAO $managerutilisateur le manager de l'utilisateur
     * @return bool
     * 
     */
    public static function mailCorrectExistePas(string $mail, string &$messageErreur ,UtilisateurDAO $managerutilisateur): bool
    {
        
        $valretour = true;
        // si le mail n'est pas valide ou si le mail existe déjà
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) || $managerutilisateur->findByMail($mail) != null) {
            
            $messageErreur = "Le mail n'est pas valide";
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @bref permet de verifier si l'utilisateur est assez vieux
     * @param string $date la date de naissance de l'utilisateur
     * @param int $age l'age minimal de l'utilisateur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool
     * 
     */
    public static function ageCorrect(string $date, int $age, string &$messageErreur ): bool
    {
        $valretour = true;
        // si l'utilisateur est trop jeune
        if (strtotime($date) >= strtotime(date("Y-m-d") . " -" . ($age * 12) . " month")) {
           
            $messageErreur = "Vous êtes trop jeune pour vous inscrire";
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
    public static function  generateToken(?string $userId, ?int $temp = 1)
    {
        // clé secrète
        $secretKey = SECRET_KEY;
        // encodage du header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // encodage du payload (les données de l'utilisateur et la date d'expiration)
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + 3600 * $temp,
            'id' => $userId,
        ]);
        // encodage en base64url
        $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        // signature
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secretKey, true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        // token
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    /**
     * @bref permet de renvoyer les informations du token id utilisateur et date d'expiration
     * @param string $token le token que l'on veut connaitre les informations
     * @return array
     */
    public static function verifyToken(?string $token): ?array
    {
        $secretKey = SECRET_KEY;
        // si le token est null
        if ($token == null) {
            return null;
        }
        // séparation du token
        list($header, $payload, $signature) = explode('.', $token);
        
        // vérification de la signature
        $validSignature = rtrim(strtr(base64_encode(hash_hmac('sha256', $header . "." . $payload, $secretKey, true)), '+/', '-_'), '=');
        if ($validSignature !== $signature) {
            return null;
        }
        // décodage du payload
        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
        // verification de la date d'expiration
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
    public static function utilisateurExiste(?Utilisateur $useur, string &$messageErreur): bool
    {
        $valretour = true;
        // si l'utilisateur n'existe pas
        if ($useur == null) {
            $valretour = false;
            
            $messageErreur = " Ce compte n'existe pas veuillez vous inscrire";
        }
        return $valretour;
    }
    /**
     * @bref permet de verifier si le mot de passe est correct et renvoie un message d'erreur si il ne l'est pas
     * @param string $mdp le mot de passe que l'on veut vérifier
     * @param string $mdpBDD le mot de passe de la base de données (correct)
     * @return bool
     */
    public static function motDePasseCorrect(string $mdp, string $mdpBDD, $utilisateur , string &$messageErreur): bool
    {
        $valretour = true;
        // si le mot de passe est incorrect
        if (!password_verify($mdp, $mdpBDD)) {
            $message = "";
            $valretour = false;
            Utilitaires::tentativeEchoue($utilisateur->getId());
            //afficher le message d'erreur si l'utilisateur a fait 5 tentatives
            if (isset($_SESSION['login_attempts'][$utilisateur->getId()]['count'])) {
                if ($_SESSION['login_attempts'][$utilisateur->getId()]['count'] == 5) {
                    $message = "Vous avez été bloqué pour 10 minutes";
                } else {
                    $message = "il vous reste " . (5 - $_SESSION['login_attempts'][$utilisateur->getId()]['count']) . " tentatives avant d'être bloqué 10 minutes";
                }
            }
            
           $messageErreur = "Le mot de passe est incorrect $message";
        }
        return $valretour;
    }
    /**
     * @bref permet de verifier si le mot de passe est robuste et renvoie un message d'erreur si il ne l'est pas
     * @param string $mdp le mot de passe que l'on veut vérifier
     * @param string $page la page sur laquelle on veut renvoyer un message d'erreur
     * @return bool
     */
    public static function estRobuste(string $mdp, string &$messageErreur): bool
    {
        $valretour = true;
        $messageErreur = "Le mot de passe doit contenir";
        // si le mot de passe ne contient pas une minuscule
        if (!preg_match('/[a-z]/', $mdp)) {
            $valretour = false;

            $messageErreur = $messageErreur + " au moins une minuscule";
        }
        // si le mot de passe ne contient pas une majuscule
        if (!preg_match('/[A-Z]/', $mdp)) {
            $valretour = false;

            $messageErreur = ($messageErreur == "Le mot de passe doit contenir") ? $messageErreur + " au moins une majuscule" : $messageErreur + ", au moins une majuscule";
        }
        // si le mot de passe ne contient pas un chiffre
        if (!preg_match('/[\d]/', $mdp)) {
            $valretour = false;

            $messageErreur = ($messageErreur == "Le mot de passe doit contenir") ? $messageErreur + " au moins un chiffre" : $messageErreur + ", au moins un chiffre";
        }
        // si le mot de passe ne contient pas un caractère spécial
        if (!preg_match('/[@$!%*?&]/', $mdp)) {
            $valretour = false;

            $messageErreur = ($messageErreur == "Le mot de passe doit contenir") ? $messageErreur + " au moins un caractère spécial" : $messageErreur + " et au moins un caractère spécial";
        }
        if ($valretour == true) {
            
            $messageErreur= "";
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
    public static function nonNull($val, string &$messageErreur): bool
    {
        $valretour = true;
        // si la valeur est null
        if ($val == null) {
            
           $messageErreur = "Ce lien n'est pas valide ";
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @bref permet de  verifier l'identifiant de l'utilisateur  n'existe pas déjà
     * @param string $id l'identifiant que l'on veut vérifier
     * 
     */
    public static function idExistePas(string $id, string &$messageErreur, UtilisateurDAO $managerutilisateur): bool
    {
        
        $valretour = true;
        // si l'identifiant existe déjà
        if ($managerutilisateur->find($id) != null) {
            
            $messageErreur = "L'identifiant est déjà utilisé";
            $valretour = false;
        }
        return $valretour;
    }


      /**
     * @bref permet de verifier si le fichier est trop lourd et renvoie un message d'erreur si il l'est
     * @param array $fichier le fichier que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param Utilisateur $utilisateur l'utilisateur sur lequel on veut renvoyer un message d'erreur
     */
    public static function fichierTropLourd(array $fichier,string $contenu,  ?string $messageErreur): bool
    {
        $valretour = true;
        // si le fichier est trop lourd (2Mo)
        if ($fichier['size'] > 2000000) {
            $valretour = false;
            
            $messageErreur = "Le fichier de $contenu est trop lourd";
        }
        return $valretour;
    }

    /**
     * @bref permet de mettre à jour l'image de l'utilisateur
     * @param array $fichier le fichier que l'on veut mettre à jour
     * @param string $type le type de fichier que l'on veut mettre à jour
     * @param Utilisateur $utilisateur l'utilisateur sur lequel on veut mettre à jour l'image
     */
    public static function ajourfichier($fichier, $type, &$messageErreur , $utilisateur)
    {
        if ($fichier['name'] != '') {
            //supprimer l'ancienne image
            if (file_exists($utilisateur->getUrlImageBanniere()) && $utilisateur->getUrlImageProfil() != "images/" . $type . "_de_base.png") {
                switch ($type) {
                    case "Profil":
                        unlink($utilisateur->getUrlImageProfil());
                        break;
                    case "Baniere":
                        unlink($utilisateur->getUrlImageBanniere());
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
                        $utilisateur->setUrlImageBanniere($fichier['name']);
                        break;
                }
            } else {

                
                $messageErreur = "Nous n'avons pas pu télécharger l'image de $type";
            }
        }
    }

    /**
     * @bref permet de verifier si ne brute force  pas la connexion
     * 
     */
    public static function isBruteForce($id_utilisateur, string &$message)
    {
        $maxAttempts = 5;
        $lockoutTime = 10 * 60; // 10 minutes
        // initialisation de la variable de session si elle n'existe pas
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = [];
        }
        // initialisation de la variable de session si elle n'existe pas
        if (!isset($_SESSION['login_attempts'][$id_utilisateur])) {
            $_SESSION['login_attempts'][$id_utilisateur] = ['count' => 0, 'last_attempt' => time()];
        }
        
        $attempts = $_SESSION['login_attempts'][$id_utilisateur];
        // s'il a fait plus de 5 tentatives en moins de 10 minutes on bloque l'id_utilisateur 
        if ($attempts['count'] >= $maxAttempts && (time() - $attempts['last_attempt']) < $lockoutTime) {
            
            $tempsenminute = round(($lockoutTime - (time() - $attempts['last_attempt'])) / 60);
            $tempsenseconde = round(($lockoutTime - (time() - $attempts['last_attempt'])) % 60);
            $message = "Vous avez été bloqué pour  $tempsenminute  minutes $tempsenseconde secondes";
            

            return true;
        }

        return false;
    }
    /**
     * @bref permet de mettre à jour le nombre de tentative de connexion échoué
     * @param string $id_utilisateur le nom de l'utilisateur
     * @return void
     */
    public static function tentativeEchoue($id_utilisateur) : void
    {
        // initialisation de la variable de session si elle n'existe pas
        if (!isset($_SESSION['login_attempts'][$id_utilisateur])) {
            $_SESSION['login_attempts'][$id_utilisateur] = ['count' => 0, 'last_attempt' => time()];
        }
        // ajouter une tentative dans la variable de session
        $_SESSION['login_attempts'][$id_utilisateur]['count']++;
        $_SESSION['login_attempts'][$id_utilisateur]['last_attempt'] = time();

    }
    /**
     * @bref permet de remettre à zéro le nombre de tentative de connexion échoué
     * @param string $id_utilisateur le nom de l'utilisateur
     */
    public static function resetBrutForce($id_utilisateur)
    {
        // remettre à zéro le nombre de tentative de connexion échoué
        if (isset($_SESSION['login_attempts'][$id_utilisateur])) {
            unset($_SESSION['login_attempts'][$id_utilisateur]);
        }
    }

    

    /**
     * @bref permet de verifier si le texte contient de la profanité
     * @param string $text le texte que l'on veut vérifier
     * @param string $contenu le contenu sur lequel on veut renvoyer un message d'erreur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool retourne vrai si le texte contient de la profanité
     */
    public static function verificationDeNom($text , string $contenu , string  &$messageErreur) : bool {

        $profanity_list = json_decode(file_get_contents("config/nomincorect.json"), true); // Vous pouvez ajouter d'autres mots
        // On parcourt chaque mot de profanité
        foreach ($profanity_list['nom'] as $word) {
            // Expression régulière pour détecter le mot de profanité avec des chiffres ou autres caractères spéciaux
            $pattern = '/' . preg_quote($word, '/') . '[0-9]*/i';  
            if (preg_match($pattern, $text)) {
                
                $messageErreur = "$contenu contient de la profanité";
                return true; // Le texte contient de la profanité
            }
        }
        return false; // Aucun mot de profanité trouvé
    }
    /**
     * @bref permet de verifier si un utilisateur est vérifié
     * @param string $id l'id de l'utilisateur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param UtilisateurDAO $managerutilisateur le manager de l'utilisateur
     */
   public static function verifUtiliateurverifier(string $id, string &$messageErreur , UtilisateurDAO $managerutilisateur) : bool {
        $valretour = true;
        // si verifie l'utilisateur n'est pas vérifié
        if (!$managerutilisateur->verificationUtilisateurValide($id)) {
            
            $messageErreur = "ce compte n'est pas vérifié veuillez vérifier votre mail pour activer votre compte";
            $valretour = false;
        }
        return $valretour;
    
   }

}
