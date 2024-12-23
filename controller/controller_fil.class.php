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
        // Récupérer les fils de discussion
        $managerFil = new FilDAO($this->getPdo());
        $threads = $managerFil->findAll();

        // Récupérer les thèmes disponibles
        $managerTheme = new ThemeDAO($this->getPdo());
        $themes = $managerTheme->findAll();

        echo $this->getTwig()->render('forum.html.twig', [
            'fils' => $threads,
            'themes' => $themes
        ]);
        exit();
    }

    /**
     * @brief Méthode d'affichage d'un fil de discussion par son identifiant
     * 
     * @details Méthode permettant d'afficher un fil de discussion par son identifiant, et ainsi de permettre l'afficahge de la discussion sous-jacente
     *
     * @return void
     */
    public function afficherFilParId(?int $id = null, ?string $messageErreur = null)
    {
        if ($id == null) {
            $id = $this->getGet()['id_fil'];
        }

        $filDAO = new FilDAO($this->getPdo());
        $fil = $filDAO->findById($id);

        $messageDAO = new MessageDAO($this->getPdo());
        $messages = $messageDAO->listerMessagesParFil($id);

        $signalements = RaisonSignalement::getAllReasons();


        echo $this->getTwig()->render('fil.html.twig', [
            'messages' => $messages,
            'fil' => $fil,
            'messageSuppr' => VALEUR_MESSAGE_SUPPRIME,
            'raisonSignalement' => $signalements,
            'messageErreur' => $messageErreur
        ]);
        exit();
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
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        }

        // Vérifier le message
        $message = htmlspecialchars($_POST['message']);

        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageOk = Utilitaires::comprisEntre($message, 1024, 10, $contenuErreur, $messageErreur);
        if (!$messageOk) {
            $this->afficherFilParId($_POST['id_fil'], $messageErreur);
            exit();
        }

        // Vérifier que l'id du fil n'est pas nul
        $idFil = intval($_POST['id_fil']);
        if ($idFil == null) {
            $this->afficherFilParId($_POST['id_fil'], "L'id du fil est invalide");
            exit();
        }

        // Récupérer l'id du message parent
        $idMessageParent = intval($_POST['id_message_parent']);
        if ($idMessageParent == null) {
            $this->afficherFilParId($_POST['id_fil'], "Message parent invalide");
            exit();
        }

        // Vérifier l'utilisateur
        if (isset($_SESSION['utilisateur'])) {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Créer le message
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, $idMessageParent, $message, $idUtilisateur);

        // Rediriger vers le fil
        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();

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
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Vérifier le message
        $message = htmlspecialchars($_POST['message']);

        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageOk = Utilitaires::comprisEntre($message, 1024, 10, $contenuErreur, $messageErreur);
        if (!$messageOk) {
            $this->afficherFilParId($_POST['id_fil'], $messageErreur);
            exit();
        }

        // Vérifier que l'id du fil n'est pas nul
        $idFil = intval($_POST['id_fil']);
        if ($idFil == null) {
            $this->afficherFilParId($_POST['id_fil'], "L'id du fil est invalide");
            exit();
        }

        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, null, $message, $idUtilisateur);

        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }

    /**
     * @brief Méthode d'ajout d'un dislike à un message
     * 
     * @return void
     */
    public function dislike()
    {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);
        $idFil = intval($_POST['id_fil']);

        // Ajouter le dislike
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, $idUtilisateur, false);

        // Rediriger vers le fil
        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }


    /**
     * @brief Méthode d'ajout d'un like à un message
     * 
     * @return void
     */
    public function like()
    {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);
        $idFil = intval($_POST['id_fil']);

        // Ajouter le dislike
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, $idUtilisateur, true);

        // Rediriger vers le fil
        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }

    /** 
     * @brief Méthode de création d'un fil de discussion
     * 
     * @details Permet la création d'un fil de discussion avec un titre et un/plusieurs thèmes
     * 
     */
    public function creerFil()
    {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Vérifier le titre
        $titre = htmlspecialchars($_POST['titre']);

        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageOk = Utilitaires::comprisEntre($titre, 50, 10, $contenuErreur, $messageErreur);
        if (!$messageOk) {
            $this->afficherFilParId($_POST['id_fil'], $messageErreur);
            exit();
        }

        // Un fil pouvant avoir 0..* thèmes, on n'a pas besoin de les vérifier. On les récupère simplement
        $themes = $_POST['themes'];

        // Vérifier le message 
        $premierMessage = htmlspecialchars($_POST['message']);

        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageOk = Utilitaires::comprisEntre($premierMessage, 1024, 10, $contenuErreur, $messageErreur);
        if (!$messageOk) {
            $this->afficherFilParId($_POST['id_fil'], $messageErreur);
            exit();
        }

        // Vérifier la description
        $description = htmlspecialchars($_POST['description']);

        $messageErreur = "";
        $contenuErreur = "Une description doit contenir";
        $messageOk = Utilitaires::comprisEntre($description, 1024, 10, $contenuErreur, $messageErreur);


        // Créer le fil
        $managerFil = new FilDAO($this->getPdo());
        $idFil = $managerFil->create($titre, $description);

        // Ajouter le premier message
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, null, $premierMessage, $idUtilisateur);

        // Ajouter les thèmes
        $managerFil->addThemes($idFil, $themes);

        // Rediriger vers le fil
        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }


    /**
     * Méthode de suppression d'un message
     * 
     * @return void
     */
    public function supprimerMessage()
    {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les infos depuis le formulaire
        $idMessageASuppr = $_POST["idMessage"];
        $idFil = $_POST["id_fil"];

        // Vérifier que le message provient bien de l'utilisateur
        $managerMessage = new MessageDAO($this->getPdo());
        $indiquePropriete = $managerMessage->checkProprieteMessage($idMessageASuppr, $idUtilisateur);

        if (!$indiquePropriete) {
            $this->afficherFilParId($idFil, "Vous ne pouvez pas supprimer un message qui ne vous appartient pas");
            exit();
        }

        // Supprimer le message et ses réactions
        $managerMessage->supprimerMessage($idMessageASuppr);
        $managerMessage->purgerReactions($idMessageASuppr);

        // Réafficher le fil
        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }

    /**
     * Méthode permettant de recueillir un signalement en BD
     * 
     * @return void
     */
    public function signalerMessage()
    {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId($_POST['id_fil'], "Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId($_POST['id_fil'], "Vous devez être connecté pour répondre à un message");
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les données du formulaire
        $idMessage = $_POST['id_message'];
        $raison = $_POST['raison'];
        $idFil = $_POST['id_fil'];

        // Vérifier la raison
        if (!RaisonSignalement::isValidReason($raison)) {
            $this->afficherFilParId($idFil, "Raison de signalement invalide");
            exit();
        }

        // Créer l'objet signalement
        $signalement = new Signalement();
        $signalement->setIdMessage($idMessage);
        $signalement->setIdUtilisateur($idUtilisateur);
        $signalement->setRaison(RaisonSignalement::fromString($raison));

        // Insérer le signalement en BD
        $managerSignalement = new SignalementDAO($this->getPdo());
        $managerSignalement->ajouterSignalement($signalement);

        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }
}
