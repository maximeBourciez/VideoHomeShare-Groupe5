<?php


class ControllerNotification extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche les notifications de l'utilisateur connecté
     * @return void
     */
    public function afficheNotifications()
    {   
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
        } else {
            // Redirection vers la page de connexion
            $managerUtilisateur  = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();

        }


        $manageurnotification = new NotificationDAO($this->getPdo());
        $notifications = $manageurnotification->findbyUser($utilisateur->getId());
        // afficher la page de notification
        $template = $this->getTwig()->load('notification.html.twig');
         echo $template->render( array("notifications" => $notifications, "utilisateur" => $utilisateur ,"nbNotification" => count($notifications)));
        
    }
    /**
     * @brief Supprime toutes les notifications de l'utilisateur connecté
     * @return void
     */
    public function suprimerTout(){
        $utilisateur = unserialize($_SESSION["utilisateur"]);
        $manageurnotification = new NotificationDAO($this->getPdo());
        // Suppression de toutes les notifications
        $manageurnotification->delete($utilisateur->getId());
        // afficher la page de notification
        $this->afficheNotifications();
    }
    /**
     * @brief Supprime une notification de l'utilisateur connecté
     * @return void
     */
    public function suprimerUneNotification(){
        // Récupération des données du formulaire
        $id = $_POST["id"];
        $utilisateur = unserialize($_SESSION["utilisateur"]);

        $manageurnotification = new NotificationDAO($this->getPdo());
        // Suppression de la notification
        $manageurnotification->deleteById($id,$utilisateur->getId());
        // afficher la page de notification
        $this->afficheNotifications();
    }
}