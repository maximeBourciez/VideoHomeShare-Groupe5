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
    public function listerThreads(?string $messageErreur = null)
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
     * @param int $idFil Identifiant du fil de discussion
     * @param bool $indiqueSuccess Indique si l'opération a réussi
     * @param string $message Message à afficher
     *
     * @return void
     */
    public function afficherFilParId()
    {

        $idFil = $_GET['id_fil'];
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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

        // Récupérer les raisons de signalement
        $raisonsSignalement = RaisonSignalement::getAllReasons();

        // Préparer les données à passer à la vue, en excluant les variables nulles
        $retour = $this->recupereMessagesFlash();
        $data = [
            'fil' => $fil,
            'messages' => $messages,
            'page_courante' => $page,
            'nombre_pages' => $nombrePages,
            'raisonsSignalement' => $raisonsSignalement,
            'messageInfos' => $retour['message'],
            'indiqueSuccess' => $retour['success']
        ];

        // Passer les données à la vue
        echo $this->getTwig()->render('fil.html.twig', $data);
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
        // Récupérer l'id du fil
        $idFil = intval($_POST['id_fil']);

        // Vérifier la méthode HTTP
        $this->verifierMethodeHTTP($idFil, "thread");

        // Vérifier que l'utilisateur est connecté
        $idUtilisateur = $this->verifierConnexion($idFil);
        if ($idUtilisateur === null) {
            return;
        }

        // Vérifier et nettoyer le message
        $message = htmlspecialchars($_POST['message']);
        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageEstValide = Utilitaires::comprisEntre($message, 1024, 10, $contenuErreur, $messageErreur);
        if (!$messageEstValide) {
            // Ajouter les messages flash
            $_SESSION['flash']['success'] = false;
            $_SESSION['flash']['message'] = $messageErreur;

            header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            return;
        }

        // Vérifier la profanité du message
        $messageEstProfane = Utilitaires::verificationDeNom($message, "Le message ", $messageErreur);
        if ($messageEstProfane) {
            // Ajouter les messages flash
            $_SESSION['flash']['success'] = false;
            $_SESSION['flash']['message'] = $messageErreur;

            $this->signalerMessage($message);
            header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            return;
        }

        // Vérifier et convertir l'ID du fil
        if ($idFil === 0) {
            // Ajouter les messages flash
            $_SESSION['flash']['success'] = false;
            $_SESSION['flash']['message'] = "ID de fil invalide";

            header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            return;
        }

        // Récupérer l'ID du message parent (si présent)
        $idMessageParent = isset($_POST['id_message_parent']) ? intval($_POST['id_message_parent']) : null;
        if (isset($_POST['id_message_parent']) && $idMessageParent === 0) {
            // Ajouter les messages flash
            $_SESSION['flash']['success'] = false;
            $_SESSION['flash']['message'] = "ID de message parent invalide";

            header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            return;
        }

        // Créer le message
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, $idMessageParent, $message, $idUtilisateur);

        // Notifier l'utilisateur si le message est une réponse
        if ($idMessageParent !== null) {
            $this->notifierUtilisateur("reponse", $idMessageParent, $idFil);
        }

        // Rediriger vers la page d'affichage du fil avec un paramètre de succès
        $_SESSION['flash']['success'] = true;
        $_SESSION['flash']['message'] = "Message ajouté avec succès";

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
        $this->verifierMethodeHTTP($idFil, "thread");

        // Vérifier que l'utilisateur est connecté
        $idUtilisateur = $this->verifierConnexion($idFil);
        if ($idUtilisateur === null) {
            return;
        }

        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);

        // Ajouter le dislike
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, $idUtilisateur, false);

        // Notifier l'utilisateur
        $this->notifierUtilisateur("reaction", $idMessage, $idFil);

        // Pattern PRG: Rediriger vers la page d'affichage du fil avec un paramètre de succès
        $_SESSION['flash']['success'] = true;
        $_SESSION['flash']['message'] = "Réaction enregistrée";

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
        $this->verifierMethodeHTTP($idFil, "thread");

        // Vérifier que l'utilisateur est connecté
        $idUtilisateur = $this->verifierConnexion($idFil);
        if ($idUtilisateur === null) {
            return;
        }

        // Récupérer les infos du message
        $idMessage = intval($_POST['id_message']);

        // Ajouter le like
        $managerReaction = new MessageDAO($this->getPdo());
        $managerReaction->ajouterReaction($idMessage, $idUtilisateur, true);

        // Notifier l'utilisateur
        $this->notifierUtilisateur("reaction", $idMessage, $idFil);

        // Pattern PRG: Rediriger vers la page d'affichage du fil avec un paramètre de succès
        $_SESSION['flash']['success'] = true;
        $_SESSION['flash']['message'] = "Réaction enregistrée";

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
        $this->verifierMethodeHTTP(null, "forum");

        // Vérifier que l'utilisateur est connecté
        $idUtilisateur = $this->verifierConnexion(null, "forum");
        if ($idUtilisateur === null) {
            return;
        }

        // Vérifier le titre
        $titre = htmlspecialchars($_POST['titre']);

        $messageErreur = "";
        $contenuErreur = "Un message doit contenir";
        $messageOk = Utilitaires::comprisEntre($titre, 50, 10, $contenuErreur, $messageErreur);
        if (!$messageOk) {

            $this->listerThreads($messageErreur);
            return;
        }

        // vérifier la profanité du titre
        $titreEstProfane = Utilitaires::verificationDeNom($titre, "Le titre ", $messageErreur);
        if ($titreEstProfane) {
            $this->signalerMessage("Tentative de création d'un thread : " . $titre);
            $this->listerThreads($messageErreur);
            return;
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
            return;
        }

        // Vérifier la profanité du premier message
        $premierMessageEstProfane = Utilitaires::verificationDeNom($premierMessage, "Le message ", $messageErreur);
        if ($premierMessageEstProfane) {
            $this->signalerMessage($premierMessage);
            $this->listerThreads($messageErreur);
            return;
        }

        // Vérifier la description
        $description = htmlspecialchars($_POST['description']);

        $messageErreur = "";
        $contenuErreur = "Une description doit contenir";
        $messageOk = Utilitaires::comprisEntre($description, 1024, 10, $contenuErreur, $messageErreur);

        // Vérifier la profanité de la description
        $descriptionEstProfane = Utilitaires::verificationDeNom($description, "La description ", $messageErreur);
        if ($descriptionEstProfane) {
            $this->signalerMessage("Tentaticve de création d'un thread (description): " . $description);
            $this->listerThreads($messageErreur);
            return;
        }

        // Créer le fil
        $managerFil = new FilDAO($this->getPdo());
        $idFil = $managerFil->create($titre, $description);

        // Ajouter le premier message
        $managerMessage = new MessageDAO($this->getPdo());
        $managerMessage->ajouterMessage($idFil, null, $premierMessage, $idUtilisateur);

        // Ajouter les thèmes
        $managerFil->addThemes($idFil, $themes);

        // Pattern PRG: Rediriger vers la page d'affichage du fil avec un paramètre de succès
        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil . "&success=true&message=" . urlencode("Fil de discussion créé avec succès"));
        exit();
    }

    /**
     * Méthode de suppression d'un message
     * 
     * @return void
     */
    public function supprimerMessage()
    {
        // Récupérer l'id du fil
        $idFil = intval($_POST['id_fil']);

        // Vérifier la méthode HTTP
        $this->verifierMethodeHTTP($idFil, "thread");

        // Vérifier que l'utilisateur est connecté
        $idUtilisateur = $this->verifierConnexion($idFil);
        if ($idUtilisateur === null) {
            return;
        }

        // Récupérer les infos depuis le formulaire
        $idMessageASuppr = intval($_POST["idMessage"]);

        // Vérifier que le message provient bien de l'utilisateur
        $managerMessage = new MessageDAO($this->getPdo());
        $indiquePropriete = $managerMessage->checkProprieteMessage($idMessageASuppr, $idUtilisateur);

        if (!$indiquePropriete) {
            // Ajouter les messages flash
            $_SESSION['flash']['success'] = false;
            $_SESSION['flash']['message'] = "Vous n'êtes pas autorisé à supprimer ce message, il ne vous appartient pas";

            header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            exit();
        }

        // Supprimer le message et ses réactions
        $managerMessage->supprimerMessage($idMessageASuppr);
        $managerMessage->purgerReactions($idMessageASuppr);

        // supprimer les signalements associés
        $managerSignalement = new SignalementDAO($this->getPdo());
        $managerSignalement->supprimerSignalementMessage($idMessageASuppr);

        // Redirioger vers la page d'affichage du fil avec un paramètre de succès
        $_SESSION['flash']['success'] = true;
        $_SESSION['flash']['message'] = "Message supprimé avec succès";

        header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
        exit();
    }

    /**
     * Méthode permettant de recueillir un signalement en BD
     * 
     * @return void
     */
    public function signalerMessage(?string $contenuSignale = null)
    {
        // Récupérer l'id du fil
        $idFil = isset($_POST['id_fil']) ? intval($_POST['id_fil']) : null;

        // Vérifier la méthode HTTP si ce n'est pas un appel interne
        if (!isset($contenuSignale)) {
            $this->verifierMethodeHTTP($idFil, "thread");
        }

        // Vérifier que l'utilisateur est connecté
        $source = ($idFil !== null) ? "" : "forum";
        $idUtilisateur = $this->verifierConnexion($idFil, $source);
        if ($idUtilisateur === null) {
            return;
        }

        // Récupérer les données du formulaire si le signalement est manuel
        if (!isset($contenuSignale)) {
            $idMessage = intval($_POST['id_message']);
            $raison = $_POST['raison'];

            // Vérifier la raison
            if (!RaisonSignalement::isValidReason($raison)) {
                // Ajouter les messages flash
                $_SESSION['flash']['success'] = false;
                $_SESSION['flash']['message'] = "Raison de signalement invalide";

                header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
                exit();
            }
        } else {
            $raison = "Contenu inapproprié";
            $idMessage = null;
        }

        // Créer l'objet signalement
        $signalement = new Signalement();

        $signalement->setIdUtilisateur($idUtilisateur);
        if (isset($contenuSignale)) {
            $signalement->setRaison(RaisonSignalement::fromString("Contenu inapproprié"));
            $signalement->setEstAutomatique(true);
            $signalement->setContenu($contenuSignale);
        } else {
            $signalement->setRaison(RaisonSignalement::fromString($raison));
            $signalement->setEstAutomatique(false);
            $signalement->setIdMessage($idMessage);
            $signalement->setContenu("");
        }

        // Insérer le signalement en BD
        $managerSignalement = new SignalementDAO($this->getPdo());
        $managerSignalement->ajouterSignalement($signalement);

        // Si c'est un appel interne, ne pas rediriger
        if (!isset($contenuSignale) && $idFil !== null) {
            // Ajouter les messages flash
            $_SESSION['flash']['success'] = true;
            $_SESSION['flash']['message'] = "Signalement enregistré";

            header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            exit();
        }

        // Pour les appels internes, juste retourner
        return;
    }

    /**
     * 
     * Méthode permettant de récupérer les messages d'un fil postés après celui d'id passé via GET
     * 
     * @details Méthode utilisée en AJAX par JS/realtime-message.js afin d'actualiser les messages du forum en temps réel pour tous les utilisateurs.
     * 
     * @return never
     */
    public function getNouveauxMessages()
    {
        if (!isset($_GET['id_fil']) || !isset($_GET['dernierMessageId'])) {
            http_response_code(400);
            exit(json_encode(['error' => 'Paramètres manquants']));
        }

        $idFil = intval($_GET['id_fil']);
        $dernierMessageId = intval($_GET['dernierMessageId']);

        $messageDAO = new MessageDAO($this->getPdo());
        $nouveauxMessages = json_encode($messageDAO->getNouveauxMessages($idFil, $dernierMessageId));

        // S'assurer qu'aucun output n'a été envoyé avant
        if (ob_get_length())
            ob_clean();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'messages' => $nouveauxMessages
        ]);
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
    private function notifierUtilisateur(string $type, int $idMessage, int $idFil)
    {
        // Récupérer l'utilisateur à l'origine de la notif (le connecté)
        $pseudoEmetteur = unserialize($_SESSION['utilisateur'])->getPseudo();

        // Réucpérer le nom du fil
        $managerFil = new FilDAO($this->getPdo());
        $nomFil = $managerFil->findById($idFil)[0]->getTitre();

        // Récupérer l'id de l'utilisateur à notifier
        $managerMessage = new MessageDAO($this->getPdo());
        $idReceveur = $managerMessage->findAuthor($idMessage);

        // Créer le contenu de la notification
        $contenu = match ($type) {
            "reponse" => "$pseudoEmetteur a répondu à un de vos messages dans le fil : $nomFil",
            "reaction" => "$pseudoEmetteur a réagi à un de vos messages dans le fil : $nomFil",
            default => null
        };

        $managerNotification = new NotificationDAO($this->getPdo());
        ;
        $managerNotification->creation($contenu, $idReceveur);
    }

    /**
     * @brief Vérifie que l'utilisateur est connecté et retourne son id le cas échéant
     * 
     * @details Retourne une vue en cas de non-connexion de l'utilisateur ou redirige
     * 
     * @param int|null $idFil Identifiant du fil de discussion
     * @param string|null $source Source de l'appel de la méthode (forum ou autre)
     * 
     * @return string|null Identifiant de l'utilisateur connecté
     */
    private function verifierConnexion(?int $idFil = null, ?string $source = null): ?string
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['utilisateur'])) {
            if ($idFil !== null) {
                // Ajouter les messages flash
                $_SESSION['flash']['success'] = false;
                $_SESSION['flash']['message'] = "Vous devez être connecté pour effectuer cette action";

                header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            } else if ($source === "forum") {
                $this->listerThreads("Vous devez être connecté pour effectuer cette action !");
            } else {
                header("Location: index.php?controller=utilisateur&methode=connexion");
            }
            return null;
        } else {
            return unserialize($_SESSION['utilisateur'])->getId();
        }
    }

    /**
     * @brief Vérification de la méthode HTTP
     * 
     * @return void
     */
    private function verifierMethodeHTTP(?int $idFil = null, ?string $source = null)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($idFil !== null) {
                // Ajouter les messages flash
                $_SESSION['flash']['success'] = false;
                $_SESSION['flash']['message'] = "Méthode HTTP invalide";

                header("Location: index.php?controller=fil&methode=afficherFilParId&id_fil=" . $idFil);
            } else if ($source === "forum") {
                $this->listerThreads("Méthode HTTP invalide");
            } else {
                header("Location: index.php?controller=utilisateur&methode=connexion");
            }
            exit();
        }
    }

    /**
     * @brief Méthode de récupération des messages flash
     * 
     * @return array Tableau contenant l'indicatif de succès et le message
     */
    private function recupereMessagesFlash(): array
    {
        // Initialiser le tableau de retour avec des valeurs par défaut
        $messages = [
            'success' => false,
            'message' => null,
        ];

        // Vérifier si des messages flash existent dans la session
        if (isset($_SESSION['flash'])) {
            // Récupérer les messages flash
            $messages['success'] = $_SESSION['flash']['success'] ?? false;
            $messages['message'] = $_SESSION['flash']['message'] ?? null;

            // Supprimer les messages flash après les avoir récupérés
            unset($_SESSION['flash']);
        }

        return $messages;
    }
}