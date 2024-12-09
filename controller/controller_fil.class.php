<?php

/**
 * @brief Classe ControllerFil
 * 
 * @details Classe permettant de gérer les actions liées aux fils de discussion du forum
 * 
 * @date 13/11/2020
 * 
 * @version 1.0
 * 
 * @note Classe héritant de la classe Controller
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */
class ControllerFil extends Controller
{

    /**
     * @brief Constructeur de la classe ControllerFil
     * 
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Méthode de listing des fils de discussion
     * 
     * @return void
     */
    public function listerThreads()
    {
        $filDAO = new FilDAO($this->getPdo());
        $threads = $filDAO->findAll();

        echo $this->getTwig()->render('forum.html.twig', [
            'fils' => $threads
        ]);
    }

    /**
     * @brief Méthode d'affichage d'un fil de discussion par son identifiant
     * 
     * @details Méthode permettant d'afficher un fil de discussion par son identifiant, et ainsi de permettre l'afficahge de la discussion sous-jacente
     *
     * @return void
     */
    /**
     * @brief Méthode d'affichage d'un fil de discussion par son identifiant
     * 
     * @details Méthode permettant d'afficher un fil de discussion par son identifiant, et ainsi de permettre l'afficahge de la discussion sous-jacente
     *
     * @return void
     */
    public function afficherFilParId(?int $id = null)
    {

        // Récupérer l'id du fil si il est nul dans le parametre
        if ($id == null) {
            $id = $this->getGet()['id_fil'];
        }

        // Récupérer les infos du fil
        $filDAO = new FilDAO($this->getPdo());
        $fil = $filDAO->findById($id);

        // Récuérer les messages du fil
        $messageDAO = new MessageDAO($this->getPdo());
        $messages = $messageDAO->listerMessagesParFil($id);

        // Rendre le template avec les infos
        echo $this->getTwig()->render('fil.html.twig', [
            'messages' => $messages,
            'fil' => $fil

        ]);
    }

    /**
     * @brief Méthode d'ajout d'un message dans un fil de discussion
     * 
     * @details Récupère les infos du message à ajouter, du message parent et du fil puis l'ajoute dans la base de données
     *  
     * @return void
     */
    public function repondre()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les infos du message
            $idFil = intval($_POST['id_fil']);
            $idMessageParent = intval($_POST['id_message_parent']);
            $message = htmlspecialchars($_POST['message']);
            if (isset($_SESSION['connecter'])) {
                //recuperer l'utilisateur connecter if
                $personneConnect = unserialize($_SESSION['connecter']);
                $idUtilisateur = $personneConnect->getId();
            }

            // Validation des données (exemple simple)
            if (empty($message)) {
                die("Le message ne peut pas être vide.");
            }

            // Créer le message
            $managerMessage = new MessageDAO($this->getPdo());
            $managerMessage->ajouterMessage($idFil, $idMessageParent, $message);

            // Rediriger vers le fil
            $this->afficherFilParId($idFil);
            exit;
        } else {
            die("Méthode non autorisée.");
        }
    }

    /**
     * @brief Méthode d'ajout d'un message dans un fil de discussion
     * 
     * @details Permet la création d'un message sans message parent au sein d'un fil déjà existant
     * 
     * @return void
     */
    public function ajouterMessage()
    {
        // Récupérer les infos du message
        $idFil = intval($_POST['id_fil']);
        $message = htmlspecialchars($_POST['message']);

        // Ajouter le message
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, null, $message);

        // Rediriger vers le fil
        $this->afficherFilParId($idFil);
    }

    /**
     * @brief Méthode d'ajout d'un dislike à un message
     * 
     * @return void
     */
    public function dislike()
    {
        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);
        $idFil = intval($_POST['id_fil']);

        // Ajouter le dislike
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, false);

        // Rediriger vers le fil
        $this->afficherFilParId($idFil);
    }


    /**
     * @brief Méthode d'ajout d'un like à un message
     * 
     * @return void
     */
    public function like()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer les infos du message
            $idMessage = intval($_POST['id_message']);
            $idFil = intval($_POST['id_fil']);

            // Ajouter le like
            $managerReaction = new MessageDAO($this->getPdo());
            $managerReaction->ajouterReaction($idMessage, true);

            // Rediriger vers le fil
            $this->afficherFilParId($idFil);
        } else {
            die("Méthode non autorisée.");
        }
    }
}
