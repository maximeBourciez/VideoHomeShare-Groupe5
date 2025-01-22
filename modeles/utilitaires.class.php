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
 * 
 * @author Sylvain Trouilh <strouilh@iutbayonne.univ-pau.fr>
 */

class Utilitaires
{    
    /**
     * @brief pemet de verifier si la valeur est compris entre deux valeurs
     * @param string $val la valeur que l'on veut vérifier
     * @param string $valmax la valeur maximal que l'on autorise
     * @param string $valmin la valeur minimal que l'on autorise
     * @param string $contenu c'est le nom du contenu sur lequel on veut renvoyer un message d'erreur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool true si la valeur est compris entre les deux valeurs false sinon
     * 
     */
    public static function comprisEntre(string $val, ?string $valmax, string $valmin, string $contenu , string & $messageErreur): bool
    {
        $valretour = true;
        // si la valeur est plus petite que la valeur minimal
        if (strlen($val) < $valmin) {

            
            $messageErreur = $contenu . " au moins " . $valmin . " caractères" ;
            $valretour = false;
        }
        // si la valeur est plus grande que la valeur maximal
        if (strlen($val) > $valmax and $valmax != null) {

            
            $messageErreur = $contenu . " au maximum " . $valmax . " caractères";
            $valretour = false;
        }
        
        return $valretour;
    }

    /**
     * @brief permet de verifier si les deux valeurs sont identiques
     * @param string $val1 la première valeur
     * @param string $val2 la deuxième valeur
     * @param string $contenu c'est nom du contenu sur lequel on veut renvoyer un message d'erreur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool true si les deux valeurs sont identiques false sinon
     * 
     */
    public static function egale(string $val1, string $val2, string $contenu , string &$messageErreur): bool
    {
        $valretour = true;
        // si les deux valeurs ne sont pas identiques
        if ($val1 != $val2) {
            
            $messageErreur = $contenu . " ne sont pas identiques";
            $valretour = false;
        }
        return $valretour;
    }

    /**
     * @brief permet de verifier si le mail est correct  
     * @param string $mail l'adresse mail que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param UtilisateurDAO $managerutilisateur le manager de l'utilisateur
     * @return bool true si le mail est correct false sinon
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
     * @brief permet de verifier si l'utilisateur est assez vieux
     * @param string $date la date de naissance de l'utilisateur
     * @param int $age l'age minimal de l'utilisateur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool true si l'utilisateur est assez vieux false sinon
     * 
     */
    public static function ageCorrect(string $date, int $age, string &$messageErreur ): bool
    {
        $valretour = true;
        // si l'utilisateur est trop jeune
        if (strtotime($date) >= strtotime(date("Y-m-d") . " -" . ($age * 12) . " month")) {
           
            $messageErreur = "Vous êtes moins de $age. Vous êtes trop jeune pour vous inscrire.";
            $valretour = false;
        }
        return $valretour;
    }
    /**
     * @brief génére un token 
     * @param string $IdUtilisateur le id de l'utilisateur que l'on veut mettre dans le token
     * @param int $temp la durée de vie du token en heure du token
     * @return string  la chaine de caractère qui est le token
     */
    public static function  generateToken(?string $IdUtilisateur, ?int $temp = 1)
    {
        // clé secrète
        $secretKey = SECRET_KEY;
        // encodage du header
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        // encodage du payload (les données de l'utilisateur et la date d'expiration)
        $payload = json_encode([
            'iat' => time(),
            'exp' => time() + 3600 * $temp,
            'id' => $IdUtilisateur,
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
     * @brief permet de renvoyer les informations du token id utilisateur et date d'expiration
     * @param string $token le token que l'on veut connaitre les informations
     * @return array tableau associatif contenant les informations du token
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
     * @brief permet de verifier si l'utilisateur existe et ranvoie un message d'erreur si il n'existe pas sur la page donnée
     * @param Utilisateur $utilisateur l'utilisateur que l'on veut vérifier si il existe
     * @param string $page la page sur laquelle on veut renvoyer un message d'erreur
     * @return bool
     */
    public static function utilisateurExiste(?Utilisateur $utilisateur, string &$messageErreur): bool
    {
        $valretour = true;
        // si l'utilisateur n'existe pas
        if ($utilisateur == null) {
            $valretour = false;
            
            $messageErreur = " Ce compte n'existe pas veuillez vous inscrire";
        }
        return $valretour;
    }
    /**
     * @brief permet de verifier si le mot de passe est correct et renvoie un message d'erreur si il ne l'est pas
     * @param string $mdp le mot de passe que l'on veut vérifier
     * @param string $mdpBDD le mot de passe de la base de données (correct)
     * @param Utilisateur $utilisateur l'utilisateur sur lequel on veut renvoyer un message d'erreur
     * @return bool true si le mot de passe est correct false sinon
     */
    public static function motDePasseCorrect(string $mdp, string $mdpBDD, Utilisateur $utilisateur , string &$messageErreur): bool
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
     * @brief permet de verifier si le mot de passe est robuste et renvoie un message d'erreur si il ne l'est pas
     * @param string $mdp le mot de passe que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
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

            $messageErreur = ($messageErreur == "Le mot de passe doit contenir") ? $messageErreur. " au moins une majuscule" : $messageErreur. ", au moins une majuscule";
        }
        // si le mot de passe ne contient pas un chiffre
        if (!preg_match('/[\d]/', $mdp)) {
            $valretour = false;

            $messageErreur = ($messageErreur == "Le mot de passe doit contenir") ? $messageErreur . " au moins un chiffre" : $messageErreur. ", au moins un chiffre";
        }
        // si le mot de passe ne contient pas un caractère spécial
        if (!preg_match('/[@$!%*?&]/', $mdp)) {
            $valretour = false;

            $messageErreur = ($messageErreur == "Le mot de passe doit contenir") ? $messageErreur . " au moins un caractère spécial" : $messageErreur . " et au moins un caractère spécial";
        }
        if ($valretour == true) {
            
            $messageErreur= "";
        }
        return $valretour;
    }
    /**
     * @brief permet de verifier si la valeur est non null et renvoie un message d'erreur si elle l'est
     * @param mixed $val
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool true si la valeur est non null false sinon
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
     * @brief permet de  verifier l'identifiant de l'utilisateur  n'existe pas déjà
     * @param string $id l'identifiant que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param UtilisateurDAO $managerutilisateur le manager de l'utilisateur
     * @return bool true si l'identifiant n'existe pas false sinon     * 
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
     * @brief permet de verifier si le fichier est trop lourd et renvoie un message d'erreur si il l'est
     * @param array $fichier le fichier que l'on veut vérifier
     * @param string $nom le nom du fichier que l'on veut vérifier 
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     */
    public static function fichierTropLourd(array $fichier, string $nom,  ?string &$messageErreur): bool
    {
        $valretour = true;
        // si le fichier est trop lourd (2Mo)
        if ($fichier['size'] > 2000000) {
            $valretour = false;
            
            $messageErreur = "Le fichier de $nom est trop lourd";
        }
        return $valretour;
    }

    /**
     * @brief permet de mettre à jour l'image de l'utilisateur
     * @param array $fichier le fichier que l'on veut mettre à jour
     * @param string $type le type de fichier que l'on veut mettre à jour
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param Utilisateur $utilisateur l'utilisateur sur lequel on veut mettre à jour l'image
     */
    public static function ajourfichier( $fichier, $type, string &$messageErreur , Utilisateur  $utilisateur)
    {
        if ($fichier['name'] != '') {
            //supprimer l'ancienne image
            if (file_exists($utilisateur->getUrlImageBanniere()) && $utilisateur->getUrlImageProfil() != "images/" . $type . "_de_base.png") {
                switch ($type) {
                    case "Profil":
                        unlink($utilisateur->getUrlImageProfil());
                        break;
                    case "Banniere":
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
                    case "Banniere":
                        $utilisateur->setUrlImageBanniere($fichier['name']);
                        break;
                }
               
            } else {

                
                $messageErreur = "Nous n'avons pas pu télécharger l'image de $type";
            }
        }
    }

    /**
     * @brief permet de verifier si ne brute force  pas la connexion
     * @param string $id_utilisateur le nom de l'utilisateur que l'on veut vérifier si il est en brute force
     * @param string $message le message d'erreur que l'on veut renvoyer
     * @return bool true si l'utilisateur est en brute force plus de 5 tentatives en moins de 10 minutes false sinon
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
     * @brief ajoute une tentative de connexion échoué a l'utilisateur concerné
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
     * @brief permet de remettre à zéro le nombre de tentative de connexion échoué
     * @param string $id_utilisateur le nom de l'utilisateur
     * @return void
     */
    public static function resetBrutForce($id_utilisateur)
    {
        // remettre à zéro le nombre de tentative de connexion échoué
        if (isset($_SESSION['login_attempts'][$id_utilisateur])) {
            unset($_SESSION['login_attempts'][$id_utilisateur]);
        }
    }

    

    /**
     * @brief permet de verifier si le texte contient de la profanité
     * @param string $text le texte que l'on veut vérifier
     * @param string $nom nom de l'élément que l'on veut vérifier
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @return bool retourne vrai si le texte contient de la profanité
     */
    public static function verificationDeNom($text , string $nom , string  &$messageErreur) : bool {

        $profanity_list = json_decode(file_get_contents("config/nomincorect.json"), true); // Vous pouvez ajouter d'autres mots
        

        // transformer le texte en texte contenant que des minuscules et sans accents et sans caractères spéciaux
        $textbien = strtolower(html_entity_decode($text)); // Mettre le texte en minuscule
       
        $textbien = preg_replace('/\s+/', '', $textbien); // Supprimer les espaces
        $textbien = preg_replace('/!/', 'i', $textbien); // Remplacer "!" par "i"
        $textbien = preg_replace('/@/', 'a', $textbien); // Remplacer "@" par "a"
        $textbien = preg_replace('/#/', 'h', $textbien); // Remplacer "#" par "h"
        $textbien = preg_replace('/\$/', 's', $textbien); // Remplacer "$" par "s"
        $textbien = preg_replace('/%/', 'p', $textbien); // Remplacer "%" par "p"
        
        $textbien = str_replace( array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'), 
        array('A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y' ), $textbien); // Remplacer les accents
       
        $textbien = preg_replace('/0/', 'o', $textbien); // Remplacer "0" par "o"
        $textbien = preg_replace('/1/', 'i', $textbien); // Remplacer "1" par "i"
        $textbien = preg_replace('/2/', 'z', $textbien); // Remplacer "2" par "z"
        $textbien = preg_replace('/3/', 'e', $textbien); // Remplacer "3" par "e"
        $textbien = preg_replace('/4/', 'a', $textbien); // Remplacer "4" par "a"
        $textbien = preg_replace('/5/', 's', $textbien); // Remplacer "5" par "s"
        $textbien = preg_replace('/6/', 'g', $textbien); // Remplacer "6" par "g"
        $textbien = preg_replace('/7/', 't', $textbien); // Remplacer "7" par "t"
        $textbien = preg_replace('/8/', 'b', $textbien); // Remplacer "8" par "b"
        $textbien = preg_replace('/9/', 'g', $textbien); // Remplacer "9" par "g"
        
        
        
        // On parcourt chaque mot de profanité
        foreach ($profanity_list['nom'] as $word) {
            // Expression régulière pour détecter le mot de profanité avec des chiffres ou autres caractères spéciaux
            $pattern = '/' . preg_quote($word, '/') . '[0-9]*/i';  

            if (preg_match($pattern, $textbien)) {
                 
                $messageErreur = "$nom contient de la profanité";
                return true; // Le texte contient de la profanité
            }
        }
        return false; // Aucun mot de profanité trouvé
    }
    /**
     * @brief permet de verifier si un utilisateur est vérifié
     * @param string $id l'id de l'utilisateur
     * @param string $messageErreur le message d'erreur que l'on veut renvoyer
     * @param UtilisateurDAO $managerutilisateur le manager de l'utilisateur
     * @return bool true si l'utilisateur est vérifié false sinon
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
   /**
    * @brief permet de verifier si la case est cochée
    * @param mixed $case la case que l'on veut vérifier
    * @param string $messageErreur le message d'erreur que l'on veut renvoyer
    * @param string $type le type d'élément que l'on veut vérifier
    * @return bool true si la case est cochée false sinon
    */
   public static function verifiecasecocher( $case, string &$messageErreur , string $type) : bool {
        $valretour = true;
        // si la case n'est pas cochée
        if ($case == null) {
            
            $messageErreur = "Vous devez cocher  $type";
            $valretour = false;
        }
        return $valretour;
   }

}
