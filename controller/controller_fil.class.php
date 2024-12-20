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

        // Récupérer les infos pour les signalements
        $signalements = RaisonSignalement::getAllReasons();

        // Rendre le template avec les infos
        echo $this->getTwig()->render('fil.html.twig', [
            'messages' => $messages,
            'fil' => $fil,
            'messageSuppr' => VALEUR_MESSAGE_SUPPRIME,
            'raisonSignalement' => $signalements
        ]);
        exit();
    }

    /**
     * @brief Méthode d'ajout d'un message dans un fil de discussion
     * 
     * @details Récupère les infos du message à ajouter, du message parent et du fil puis l'ajoute dans la base de données
     * 
     * @todo Raise exception si user non connecté
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
            if (isset($_SESSION['utilisateur'])) {

                $personneConnect = unserialize($_SESSION['utilisateur']);
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
            $this->genererVue($idFil);
            exit();
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idFil = isset($_POST['id_fil']) ? intval($_POST['id_fil']) : null;
            $message = isset($_POST['message']) ? htmlspecialchars($_POST['message']) : null;

            if (!isset($_SESSION["utilisateur"])) {
                throw new Exception("Accès interdit");
            }

            $managerMessage = new MessageDAO($this->getPdo());
            $managerMessage->ajouterMessage($idFil, null, $message);

            $this->genererVue($idFil);
            exit();
        } else {
            throw new Exception("Méthode HTTP invalide");
        }
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
        $this->genererVue($idFil);
        exit();
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
            $this->genererVue($idFil);
            exit();
        } else {
            throw new Exception("accesInterdit");
        }
    }

    /** 
     * @brief Méthode de création d'un fil de discussion
     * 
     * @details Permet la création d'un fil de discussion avec un titre et un/plusieurs thèmes
     * 
     */
    public function creerFil()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur'])) {
            // Récupérer les données 
            $titre = htmlspecialchars($_POST['titre']);
            $themes = $_POST['themes'];
            $description = htmlspecialchars($_POST['description']);
            $premierMessage = htmlspecialchars($_POST['premierMessage']);

            // Créer le fil
            $managerFil = new FilDAO($this->getPdo());
            $idFil = $managerFil->create($titre, $description);

            // Ajouter le premier message
            $managerMessage = new MessageDAO($this->getPdo());
            $managerMessage->ajouterMessage($idFil, null, $premierMessage);

            // Ajouter les thèmes
            $managerFil->addThemes($idFil, $themes);

            // Rediriger vers le fil
            $this->genererVue($idFil);
            exit();
        } else {
            throw new Exception("accesInterdit");
        }
    }

    /**
     * Fonction d'affichage de la vue
     * 
     * @param int $idFIl Identifiant BD du fil à charger
     * 
     * @return void
     */
    private function genererVue(int $idFil)
    {
        // Envoyer le script pour le refresh de la requête
        echo "<script>
                history.replaceState({}, '', 'index.php?controller=fil&methode=afficherFilParId&id_fil=$idFil');
                window.location.reload();
              </script>";
    }

    /**
     * Méthode de suppression d'un message
     * 
     * @return void
     */
    public function supprimerMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur'])) {
            // Récupérer les infos depuis le formulaire
            $idMessageASuppr = $_POST["idMessage"];
            $idFil = $_POST["id_fil"];

            // Récupérer l'id de l'utilisateur 
            $idUser = trim(unserialize($_SESSION["utilisateur"])->getId());

            // Vérifier que le message provient bien de l'utilisateur
            $managerMessage = new MessageDAO($this->getPdo());
            $indiquePropriete = $managerMessage->checkProprieteMessage($idMessageASuppr, $idUser);

            if (!$indiquePropriete) {
                throw new Exception("accesInterdit");
            }

            // Supprimer le message
            $managerMessage->supprimerMessage($idMessageASuppr);
            $managerMessage->purgerReactions($idMessageASuppr);

            // Réafficher le fil
            $this->genererVue($idFil);
            exit();
        } else {
            throw new Exception("accesInterdit");
        }
    }

    /**
     * Méthode permettant de recueillir un signalement en BD
     * 
     * @return void
     */
    public function signalerMessage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['utilisateur'])) {
            // Récupérer les données du formulaire
            $idMessage = $_POST['id_message'];
            $raison = $_POST['raison'];
            $idFIl = $_POST['id_fil'];

            // Récupérer les données de l'utilisateur
            $idUser = trim(unserialize($_SESSION['utilisateur'])->getId());

            // Créer l'objet signalement
            $signalement = new Signalement();
            $signalement->setIdMessage($idMessage);
            $signalement->setIdUtilisateur($idUser);
            $signalement->setRaison(RaisonSignalement::fromString($raison));            

            // Insérer le signalement en BD
            $managerSignalement = new SignalementDAO($this->getPdo());
            $managerSignalement->ajouterSignalement($signalement);

            $this->genererVue($idFIl);
            exit();
        } else {
            throw new Exception("accesInterdit");
        }
    }
}
