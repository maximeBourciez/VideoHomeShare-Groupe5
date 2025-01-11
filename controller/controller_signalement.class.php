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
class ControllerSignalement extends Controller
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
        echo $template->render(array("signalements" => $signalements));
    }

}