<?php

// Fichier des mots interdits
define("FICHIER_JSON", "config/nomincorect.json");


/**
 * @brief Classe ControllerSignalement
 * 
 * @details Classe permettant de gérer les signalements
 * 
 * @date 11/01/2025
 * 
 * @version 1.0
 * 
 * @note Classe héritant de la classe Controller
 * 
 * @author Sylvain Trouilh <strouilh@iutbayonne.unniv-pau.fr>
 */
class ControllerDashboard extends Controller
{
    /**
     * @brief Constructeur de la classe ControllerSignalement
     * 
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche les signalements
     * 
     * @return void
     */
    public function afficheSignalements(): void
    {
        // Vérification de la session
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            if ($utilisateur->getRole()->toString() != "Moderateur") {
                // Redirection vers la page d'accueil
                $managerAccueil = new ControllerIndex($this->getTwig(), $this->getLoader());
                $managerAccueil->index();
            }
        } else {
            // Redirection vers la page de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
        }

        // Récupération des signalements
        $managerSignalement = new SignalementDAO($this->getPdo());
        $signalements = $managerSignalement->findAllMessageSignaleAssoc();

        // Affichage de la page des signalements
        $template = $this->getTwig()->load('signalement.html.twig');
        echo $template->render(array("signalements" => $signalements, "nbSignalements" => count($signalements)));
    }

    /**
     * @brief Supprime un message signalé
     * 
     * @return void
     */
    public function supprimerMessageSignale(): void
    {
        // Vérification de la session
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            // Vérification  si l'utilisateur est un modérateur
            if ($utilisateur->getRole()->toString() != "Moderateur") {
                // Redirection vers la page d'accueil
                $managerAccueil = new ControllerIndex($this->getTwig(), $this->getLoader());
                $managerAccueil->index();
            }
        } else {
            // Redirection vers la page de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
        }

        // Récupération de l'identifiant du message signalé
        $idMessage = htmlspecialchars($_POST['idMessage']);

        // Suppression du message signalé
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->supprimerMessage($idMessage);
        $managerMessage->purgerReactions($idMessage);
        //suppression des signalements associés
        $managerSignalement = new SignalementDAO($this->getPdo());
        $managerSignalement->supprimerSignalementMessage($idMessage);

        // Redirection vers la page des signalements
        $this->afficheSignalements();
    }

    /**
     * 
     * @brief Fonction de suppression d'un signalement
     * @return void
     */
    public function supprimerSignalement(): void
    {
        // Vérification de la session
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            // Vérification  si l'utilisateur est un modérateur
            if ($utilisateur->getRole()->toString() != "Moderateur") {
                // Redirection vers la page d'accueil
                $managerAccueil = new ControllerIndex($this->getTwig(), $this->getLoader());
                header('Location: index.php?controller=dashboard&methode=afficheSignalements');
                exit();
            }

        } else {
            // Redirection vers la page de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
        }

        // Récupération de l'identifiant du message signalé
        $idMessage = htmlspecialchars($_POST['idMessage']);


        //suppression des signalements associés
        $managerSignalement = new SignalementDAO($this->getPdo());
        $managerSignalement->supprimerSignalementMessage($idMessage);

        // Redirection vers la page des signalements
        $this->afficheSignalements();
    }


    // Fonctions à compléter
    function afficherUtilisateurs(?string $eventuelsFiltres)
    {
    }

    /**
     * @brief Fonction de bannissement d'un utilisateur
     * @param string|null $idUtilisateur
     * @return void
     */
    function bannirUtilisateur(?string $idUtilisateur = null)
    {
        if (isset($_SESSION['utilisateur'])) {

            $utilisateur = unserialize($_SESSION['utilisateur']);
            if ($utilisateur->getRole()->toString() != "Moderateur") {
                // Redirection vers la page d'accueil
                $managerAccueil = new ControllerIndex($this->getTwig(), $this->getLoader());
                $managerAccueil->index();
                exit();
            }
        } else {
            // Redirection vers la page de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();

        }

        $idUtilisateur = htmlspecialchars($_POST['idUtilisateur']);
        $raison = htmlspecialchars($_POST['raison']);
        $dateF = new DateTime(htmlspecialchars($_POST['dateF']));
        $managerBannissement = new BannissementDAO($this->getPdo());


        $managerBannissement->create($raison, $idUtilisateur, $dateF);

        // affichage de la page des signalements
        $this->afficherUtilisateurs(null);

    }

    public function debannirUtilisateur()
    {
        // Vérifier que l'utilisateur est bien le modérateur
        if ($this->utilisateurEstModerateur()) {
            // Récupérer l'identifiant de l'utilisateur 
            $idUtilisateurADebannir = $_POST["idUtilisateur"];

            // Débannir l'utilisateur 
            $managerBannissement = new BannissementDAO($this->getPdo());
            $managerBannissement->revokeBan($idUtilisateur);

            // Réafficher la page avec un message
        }
    }

    /**
     * @brief Fonction d'affichage de la page d'administration
     * 
     * @details Récupère les backups de la BD et les derniers mots de la liste de mots interdits pour les afficher
     * 
     * @param bool $erreur Indique si une erreur est survenue
     * @param string $message Message à afficher (erreur ou succès) 
     * 
     * @return void 
     */
    public function afficherAdministration(?bool $erreur = false, ?string $message = "")
    {
        if ($this->utilisateurEstModerateur()) {
            // Récupération des backups
            $backups = $this->recupererBackups();

            // Récupération des mots interdits ajoutés (les plus loins dans la liste)
            $motsInterdits = $this->recupererMotsInterdits();

            // Déterminer la clé pour le message
            $messageKey = $erreur ? 'messageErreur' : 'messageSucces';

            // Affichage de la page d'administration
            $template = $this->getTwig()->load('pageAdministration.html.twig');
            echo $template->render([
                "backups" => $backups,
                "motsInterdits" => $motsInterdits,
                $messageKey => $message
            ]);
        }
    }



    /**
     * @brief Fonction de restauration d'une sauvegarde de la BD
     * 
     * @return void
     */
    public function restaurerSauvegarde()
    {
        // Vérifier que l'utilisateur est bien un Modérateur
        if ($this->utilisateurEstModerateur()) {

            // Récupérer la Backup à restaurer
            $fileToRestore = $_POST["backup"];

            // Tenter une restoration de la BD
            try {
                // Restaurer 
                $managerBD = BD::getInstance();
                $managerBD->restore($fileToRestore);
                $this->afficherAdministration(false, "La restauration a été effectuée avec succès");
                exit();
            } catch (Exception $e) {
                $this->afficherAdministration(true, "Une erreur est survenue lors de la restauration : " . $e->getMessage());
            }
        } else {
            // Redirection vers la page d'accueil
            header("Location: index.php?controller=index&methode=index");
        }
    }


    /**
     * @brief Méthode de sauvegarde de la base de données
     * 
     * @return void
     */
    public function sauvegarderBD()
    {
        // Vérifier que l'utilisateur est bien un Modérateur
        if ($this->utilisateurEstModerateur()) {
            // Sauvegarder la BD
            $managerBD = BD::getInstance();

            // Sauvegarder la BD
            try {
                $managerBD->sauvegarder();
                $this->afficherAdministration(false, "La sauvegarde a été effectuée avec succès");
                exit();
            } catch (Exception $e) {
                $this->afficherAdministration(true, "Une erreur est survenue lors de la sauvegarde : " . $e->getMessage());
                exit();
            }
        } else {
            // Redirection vers la page d'accueil
            header("Location: index.php?controller=index&methode=index");
            exit();
        }
    }


    /**
     * @brief Fonction de vérification que l'utilisateur est modérateur
     * 
     * @return bool $estModerateur Indique si l'utilisateur est modérateur
     */
    private function utilisateurEstModerateur()
    {
        // Vérifier que l'utilisateur est connecté et modérateur
        if (isset($_SESSION["utilisateur"])) {
            $utilisateur = unserialize($_SESSION["utilisateur"]);
            if ($utilisateur->getRole()->toString() != "Moderateur") {
                // Redirection vers la page d'accueil
                header("Location: index.php?controller=index&methode=index");
                return false;
            }
        } else {
            // Redirection vers la page de connexion
            header("Location: index.php?controller=utilisateur&methode=connexion");
            return false;
        }

        // L'utilisateur est un modérateur
        return true;
    }


    /**
     * @brief Fonction de récupération des backups de la base de données
     * 
     * @return array $backups Tableau contenant les backups de la base de données
     */
    private function recupererBackups()
    {
        $backups = array();
        $dossier = "backupsBD/";
        $fichiers = scandir($dossier);
        foreach ($fichiers as $fichier) {
            if ($fichier != "." && $fichier != "..") { // On entre dans les fichiers 

                // Récupérer les données qui nous intéressent (nom du fichier, date de création, taille)
                $infosFichier = stat($dossier . $fichier);
                $taille = round($infosFichier["size"] / 1024, 2);
                $date = date("d/m/Y H:i:s", $infosFichier["mtime"]);
                $backups[] = array("nom" => $fichier, "date" => $date, "taille" => $taille);
            }
        }
        return $backups;
    }


    /**
     * @brief Fonction de récupération de toute la liste de mots interdits
     * 
     * @return array $motsInterdits Tableau contenant les 40 derniers mots interdits ajoutés
     */
    private function recupererMotsInterdits()
    {
        // Lire le contenu du fichier JSON
        $contenu = file_get_contents("config/nomincorect.json");

        // Décoder le JSON en tableau associatif
        $data = json_decode($contenu, true);

        // Vérifier si la clé "nom" existe et est un tableau
        if (isset($data['nom']) && is_array($data['nom'])) {
            return $data['nom'];
        }

        // Retourner un tableau vide en cas d'erreur
        return [];
    }


    /**
     * @brief Fonction d'ajout d'un mot interdit
     * 
     * @return void
     */
    public function ajouterMotInterdit()
    {
        // Vérifier que l'utilisateur est bien un Modérateur
        if ($this->utilisateurEstModerateur()) {
            // Récupérer le mot interdit à ajouter
            $motInterdit = htmlspecialchars($_POST["mot"]);

            // Lire le contenu du fichier JSON
            $contenu = file_get_contents(FICHIER_JSON);

            // Décoder le JSON en tableau associatif
            $data = json_decode($contenu, true);

            // Vérifier si la clé "nom" existe et est un tableau
            if (!isset($data['nom']) || !is_array($data['nom'])) {
                $data['nom'] = [];
            }

            // Vérifier si le mot interdit n'existe pas déjà
            if (in_array($motInterdit, $data['nom'])) {
                // Redirection vers la page d'administration
                $this->afficherAdministration(true, "Le mot interdit existe déjà");
                exit();
            }

            // Ajouter le mot interdit
            $data['nom'][] = $motInterdit;

            // Encoder le tableau en JSON
            $contenu = json_encode($data);

            // Écrire le contenu dans le fichier
            file_put_contents(FICHIER_JSON, $contenu);

            // Redirection vers la page d'administration
            $this->afficherAdministration(false, "Le mot interdit a été ajouté avec succès");
            exit();
        } else {
            // Redirection vers la page d'accueil
            header("Location: index.php?controller=index&methode=index");
            exit();
        }
    }


    /**
     * @brief Fonction de suppression d'un mot interdit
     * 
     * @return void
     */
    public function supprimerMotInterdit()
    {
        // Vérifier que l'utilisateur est bien un Modérateur
        if ($this->utilisateurEstModerateur()) {
            // Récupérer le mot interdit à supprimer
            $motInterdit = htmlspecialchars($_POST["mot"]);

            // Lire le contenu du fichier JSON
            $contenu = file_get_contents(FICHIER_JSON);

            // Décoder le JSON en tableau associatif
            $data = json_decode($contenu, true);

            // Vérifier si la clé "nom" existe et est un tableau
            if (isset($data['nom']) && is_array($data['nom'])) {
                // Vérifier si le mot interdit existe
                if (!in_array($motInterdit, $data['nom'])) {
                    // Redirection vers la page d'administration
                    $this->afficherAdministration(true, "Le mot interdit n'existe pas");
                    exit();
                }

                // Supprimer le mot interdit
                $index = array_search($motInterdit, $data['nom']);
                if ($index !== false) {
                    unset($data['nom'][$index]);
                }

                // Encoder le tableau en JSON
                $contenu = json_encode($data);

                // Écrire le contenu dans le fichier
                file_put_contents(FICHIER_JSON, $contenu);

                // Redirection vers la page d'administration
                $this->afficherAdministration(false, "Le mot interdit a été supprimé avec succès");
                exit();
            } else {
                // Redirection vers la page d'accueil
                header("Location: index.php?controller=index&methode=index");
                exit();
            }
        } else {
            // Redirection vers la page d'accueil
            header("Location: index.php?controller=index&methode=index");
            exit();
        }
    }
    /**
     * @brif affiche la page de banissement
     * @return void
     */
    public function afficherBanisement()
    {
        // Vérification de la session
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            if ($utilisateur->getRole()->toString() != "Moderateur") {
                // Redirection vers la page d'accueil
                $managerAccueil = new ControllerIndex($this->getTwig(), $this->getLoader());
                $managerAccueil->index();
            }
        } else {
            // Redirection vers la page de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
        }
        $idMessage = htmlspecialchars($_POST['idmessage']);

        $managerMessage = new MessageDAO($this->getPdo());
        $managerBannissement = new BannissementDAO($this->getPdo());
        $message = $managerMessage->chercherMessageParId($idMessage);

        // Récupération des utilisateurs
        $managerUtilisateur = new UtilisateurDAO($this->getPdo());
        $utilisateur = $managerUtilisateur->find($message->getUtilisateur()->getId());
        ;
        $banisements = $managerBannissement->toutlesBanUsuer($utilisateur->getId());

        // Affichage de la page des signalements
        $template = $this->getTwig()->load('bannirUtilisateur.html.twig');
        echo $template->render(array("utilisateur" => $utilisateur, "banisements" => $banisements));
    }
}
