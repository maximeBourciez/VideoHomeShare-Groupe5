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
    public function afficheSignalements() : void
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
        echo $template->render(array("signalements" => $signalements , "nbSignalements" => count($signalements)));
    }

    /**
     * @brief Supprime un message signalé
     * 
     * @return void
     */
    public function supprimerMessageSignale() : void
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
     * @brief Supprime les signalement  un message signalé
     * 
     * @return void
     */
    public function supprimerSignalement() : void
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
    function afficherUtilisateurs(?string $eventuelsFiltres){

    }

    function afficherAdministration(){

    }

    function bannirUtilisateur(?string $idUtilisateur = null){

        $utilisateur = unserialize($_SESSION['utilisateur']);
        if ($utilisateur->getRole()->toString() != "Moderateur") {
            // Redirection vers la page d'accueil
            $managerAccueil = new ControllerIndex($this->getTwig(), $this->getLoader());
            $managerAccueil->index();
            exit();
        }

        $idMessage = htmlspecialchars($_POST['idMessage']);
        $raison = htmlspecialchars($_POST['raison']);
        $managerBannissement = new BannissementDAO($this->getPdo());

        $managerMessage = new MessageDAO($this->getPdo());
        $messge = $managerMessage->chercherMessageParId($idMessage);
        $idUtilisateur = $messge->getUtilisateur()->getId();
        $managerBannissement->create($raison,$idUtilisateur);

        // affichage de la page des signalements
        $this->afficherUtilisateurs(null);   

    }

    function debannirUtilisateur(?string $idUtilisateur){

    }

}