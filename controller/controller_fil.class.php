<?php

/**
 * @brief Classe ControllerFil
 * 
 * @details Classe permettant de gérer les actions liées aux fils de discussion du forum
 * 
 * @date Création 13/11/2024 Dernière modification 11/01/2025
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
    public function listerThreads(?string $messageErreur = null )
    {
        // Récupérer les fils de discussion
        $managerFil = new FilDAO($this->getPdo());
        $threads = $managerFil->findAll();

        // Récupérer les thèmes disponibles
        $managerTheme = new ThemeDAO($this->getPdo());
        $themes = $managerTheme->findAll();

        echo $this->getTwig()->render('forum.html.twig', [
            'fils' => $threads,
            'themes' => $themes,
            'messageErreur' => $messageErreur
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
    public function afficherFilParId(?string $messageErreur = null)
    {
        $idFil = $_GET['id_fil'];
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $messagesParPage = NOMBRE_MESSAGES_PAR_PAGE;

        // Récupérer le fil
        $filDAO = new FilDAO($this->getPdo());
        $fil = $filDAO->findById($idFil);
        
        // Récupérer les messages paginés
        $messageDAO = new MessageDAO($this->getPdo());
        $messages = $messageDAO->getMessagesPagines($idFil, $page, $messagesParPage);
        
        // Calculer le nombre total de pages
        $totalMessages = $messageDAO->getNombreMessagesParent($idFil);
        $nombrePages = ceil($totalMessages / $messagesParPage);

        // Passer les données à la vue
        echo $this->getTwig()->render('fil.html.twig', [
            'fil' => $fil,
            'messages' => $messages,
            'page_courante' => $page,
            'nombre_pages' => $nombrePages,
            'messageErreur' => $messageErreur
        ]);
        exit();
    }

    /**
     * @brief Méthode d'ajout d'un message (avec ou sans message parent) dans un fil de discussion
     * 
     * @details Gère l'ajout d'un message dans un fil de discussion, qu'il s'agisse d'une réponse ou d'un message initial.
     * 
     * @return void
     */
    public function ajouterMessage()
    {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId();
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId();
            exit();
        }
        $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();

        // Vérifier et nettoyer le message
        $message = htmlspecialchars($_POST['message']);
        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageEstValide = Utilitaires::comprisEntre($message, 1024, 10, $contenuErreur, $messageErreur);
        if (!$messageEstValide) {
            $this->afficherFilParId();
            exit();
        }

        // Vérifier et convertir l'ID du fil
        $idFil = intval($_POST['id_fil']);
        if ($idFil === 0) {
            $this->afficherFilParId();
            exit();
        }

        // Récupérer l'ID du message parent (si présent)
        $idMessageParent = isset($_POST['id_message_parent']) ? intval($_POST['id_message_parent']) : null;
        if (isset($_POST['id_message_parent']) && $idMessageParent === 0) {
            $this->afficherFilParId();
            exit();
        }

        // Créer le message
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, $idMessageParent, $message, $idUtilisateur);

        // Récupérer l'id du message
        $idMessage = $this->getPdo()->lastInsertId();

        // Notifier l'utilisateur si le message est une réponse
        if ($idMessageParent !== null) {
            $this->notifierUtilisateur("reponse", $idMessageParent, $idFil);
        }

        // Rediriger vers le fil
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
        // Récupérer l'id du fil 
        $idFil = intval($_POST['id_fil']);

        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId();
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId();
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);

        // Ajouter le dislike
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, $idUtilisateur, false);

        // Notifier l'utilisateur
        $this->notifierUtilisateur("reaction", $idMessage, $idFil);

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
        // Récupérer l'id du fil 
        $idFil = intval($_POST['id_fil']);

        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId();
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId();
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);

        // Ajouter le dislike
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, $idUtilisateur, true);

        // Notifier l'utilisateur
        $this->notifierUtilisateur("reaction", $idMessage, $idFil);

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
            $this->listerThreads("Méthode HTTP invalide");
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->listerThreads("Vous devez être connecté pour créer un fil");
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
            $this->listerThreads($messageErreur);
            exit();
        }

        // Un fil pouvant avoir 0..* thèmes, on n'a pas besoin de les vérifier. On les récupère simplement
        $themes = isset($_POST['themes']) ? $_POST['themes'] : [];

        // Vérifier le message 
        $premierMessage = htmlspecialchars($_POST['premierMessage']);

        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageOk = Utilitaires::comprisEntre($premierMessage, 1024, 10, $contenuErreur, $messageErreur);
        if (!$messageOk) {
            $this->listerThreads($messageErreur);
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
            $this->afficherFilParId();
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId();
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les infos depuis le formulaire
        $idMessageASuppr = intval($_POST["idMessage"]);
        $idFil = intval($_POST["id_fil"]);

        // Vérifier que le message provient bien de l'utilisateur
        $managerMessage = new MessageDAO($this->getPdo());
        $indiquePropriete = $managerMessage->checkProprieteMessage($idMessageASuppr, $idUtilisateur);

        if (!$indiquePropriete) {
            $this->afficherFilParId();
            exit();
        }

        // Supprimer le message et ses réactions
        $managerMessage->supprimerMessage($idMessageASuppr);
        $managerMessage->purgerReactions($idMessageASuppr);

        // supprimer les signalements associés
        $managerSignalement = new SignalementDAO($this->getPdo());
        $managerSignalement->supprimerSignalementMessage($idMessageASuppr);

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
        // Récupérer l'id du fil
        $idFil = intval($_POST['id_fil']);

        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->afficherFilParId();
            exit();
        }

        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            $this->afficherFilParId();
            exit();
        } else {
            $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        }

        // Récupérer les données du formulaire
        $idMessage = intval($_POST['id_message']);
        $raison = $_POST['raison'];
        

        // Vérifier la raison
        if (!RaisonSignalement::isValidReason($raison)) {
            $this->afficherFilParId();
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


    /**
     * Méthode permettant de notifier un utilisateur
     * 
     * @param string $type Type de notification
     * @param int $idMessage Identifiant du message 
     * @param int $idFil Identifiant du fil
     * 
     * @return void
     */
    private function notifierUtilisateur(string $type, int $idMessage, int $idFil){
        // Récupérer l'utilisateur à l'origine de la notif (le connecté)
        $pseudoEmetteur = unserialize($_SESSION['utilisateur'])->getPseudo(); 

        // Réucpérer le nom du fil
        $managerFil = new FilDAO($this->getPdo());
        $nomFil = $managerFil->findById($idFil)[0]->getTitre();

        // Récupérer l'id de l'utilisateur à notifier
        $managerMessage = new MessageDAO($this->getPdo());
        $idReceveur = $managerMessage->findAuthor($idMessage);

        // Créer le contenu de la notification
        $contenu = match($type) {
            "reponse" => "$pseudoEmetteur a répondu à un de vos messages dans le fil : $nomFil",
            "reaction" => "$pseudoEmetteur a réagi à un de vos messages dans le fil : $nomFil",
            default => null
        };

        $managerNotification = new NotificationDAO($this->getPdo()); ;
        $managerNotification->creation( $contenu , $idReceveur);

    }

    public function getNouveauxMessages() {
        if (!isset($_GET['id_fil']) || !isset($_GET['dernierMessageId'])) {
            http_response_code(400);
            exit(json_encode(['error' => 'Paramètres manquants']));
        }

        $idFil = intval($_GET['id_fil']);
        $dernierMessageId = intval($_GET['dernierMessageId']);
        
        $messageDAO = new MessageDAO($this->getPdo());
        $nouveauxMessages = json_encode($messageDAO->getNouveauxMessages($idFil, $dernierMessageId));
        
        // S'assurer qu'aucun output n'a été envoyé avant
        if (ob_get_length()) ob_clean();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $nouveauxMessages
        ]);
        exit();
    }
}
