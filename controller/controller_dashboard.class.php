<?php

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


    // Fonctions à compléter
    function afficherUtilisateurs(?string $eventuelsFiltres) {}


    function bannirUtilisateur(?string $idUtilisateur) {}

    function debannirUtilisateur(?string $idUtilisateur) {}

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
}
