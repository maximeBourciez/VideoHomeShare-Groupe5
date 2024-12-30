<?php


class ControllerNotification extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }


    public function affishesNotifications()
    {   
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
        } else {
            // Redirection vers la page de connexion
            $controleru = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $controleru->connexion();

        }


        $manageurnotification = new NotificationDAO($this->getPdo());
        $notifications = $manageurnotification->findbyUser($utilisateur->getId());

        $template = $this->getTwig()->load('notification.html.twig');
         echo $template->render( array("notifications" => $notifications, "utilisateur" => $utilisateur ,"nbNotification" => count($notifications)));
        
    }

    public function suprimerTout(){
        $utilisateur = unserialize($_SESSION["utilisateur"]);
        $manageurnotification = new NotificationDAO($this->getPdo());
        // Suppression de toutes les notifications
        $manageurnotification->delete($utilisateur->getId());
        $this->affishesNotifications();
    }

    public function suprimerUneNotification(){
        // Récupération des données du formulaire
        $id = $_POST["id"];
        $utilisateur = unserialize($_SESSION["utilisateur"]);

        $manageurnotification = new NotificationDAO($this->getPdo());
        // Suppression de la notification
        $manageurnotification->deleteById($id,$utilisateur->getId());
        $this->affishesNotifications();
    }
}