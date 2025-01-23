<?php

/**
 * @brief Contrôleur gérant les commentaires
 * 
 * Cette classe gère la création et la gestion des commentaires
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class ControllerCommentaire extends Controller
{
    private const TYPE_CONTENU = 'contenu';
    private const TYPE_COLLECTION = 'collection';
    private const TYPE_SERIE = 'serie';

    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Point d'entrée pour la création de commentaire
     */
    public function createCommentaire(): void
    {
        $type = isset($_POST['idContenu']) ? self::TYPE_CONTENU : (isset($_POST['idCollection']) ? self::TYPE_COLLECTION : (isset($_POST['idSerie']) ? self::TYPE_SERIE : null));

        if ($type === null) {
            throw new Exception("Type de commentaire non spécifié");
        }
        $this->handleCommentaire($type);
    }

    /**
     * @brief Crée un nouveau commentaire pour un contenu
     */
    public function createCommentaireContenu(): void
    {
        $this->handleCommentaire(self::TYPE_CONTENU);
    }

    /**
     * @brief Crée un nouveau commentaire pour une collection
     */
    public function createCommentaireCollection(): void
    {
        $this->handleCommentaire(self::TYPE_COLLECTION);
    }

    /**
     * @brief Crée un nouveau commentaire pour une serie
     */
    public function createCommentaireSerie(): void
    {
        $this->handleCommentaire(self::TYPE_SERIE);
    }

    /**
     * @brief Gère la création d'un commentaire
     */
    private function handleCommentaire(string $type): void
    {
        $config = $this->getConfig($type);

        // Vérification de la session
        $utilisateur = isset($_SESSION['utilisateur']) ? unserialize($_SESSION['utilisateur']) : null;
        if (!$utilisateur) {
            $template = $this->getTwig()->load($config['template']);
            echo $template->render(['messagederreur' => 'Vous devez être connecté pour poster un commentaire.']);
            return;
        }

        // Récupération et nettoyage des données POST
        $idTmdb = isset($_POST[$config['idField']]) ? htmlspecialchars($_POST[$config['idField']]) : null;
        $titre = isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : null;
        $note = isset($_POST['note']) ? (int)htmlspecialchars($_POST['note']) : null;
        $commentaireTexte = isset($_POST['commentaire']) ? htmlspecialchars($_POST['commentaire']) : null;

        // Validation des données
        $message = "";
        if (
            !Utilitaires::comprisEntre($titre, 100, 3, "Le titre doit contenir", $message) ||
            !Utilitaires::comprisEntre($commentaireTexte, 1000, 10, "Le commentaire doit contenir", $message)
        ) {

            $this->redirectWithError($type, $idTmdb, $message);
            return;
        }

        try {
            // Création de l'objet commentaire
            $commentaire = new Commentaire(
                $utilisateur->getId(),
                $titre,
                $note,
                $commentaireTexte,
                $note >= 3,
                $type === self::TYPE_CONTENU ? $idTmdb : null,
                $type === self::TYPE_COLLECTION ? $idTmdb : null,
                $type === self::TYPE_SERIE ? $idTmdb : null
            );

            // Sauvegarde dans la base de données
            $commentaireDAO = new CommentaireDAO($this->getPdo());
            $method = $config['createMethod'];
            $commentaireDAO->$method($commentaire);

            // Redirection avec message de succès
            $this->redirectWithSuccess($type, $idTmdb);
        } catch (Exception $e) {
            $this->redirectWithError($type, $idTmdb, $e->getMessage());
        }
    }

    /**
     * @brief Récupère la configuration selon le type de commentaire
     */
    private function getConfig(string $type): array
    {
        $configs = [
            self::TYPE_CONTENU => [
                'template' => 'pageDunContenu.html.twig',
                'idField' => 'idContenu',
                'controller' => 'ControllerContenu',
                'createMethod' => 'createCommentaireContenu',
                'displayMethod' => 'afficherContenu'
            ],
            self::TYPE_COLLECTION => [
                'template' => 'pageDuneCollection.html.twig',
                'idField' => 'idCollection',
                'controller' => 'ControllerCollection',
                'createMethod' => 'createCommentaireCollection',
                'displayMethod' => 'afficherCollection'
            ],
            self::TYPE_SERIE => [
                'template' => 'pageDuneSerie.html.twig',
                'idField' => 'idSerie',
                'controller' => 'ControllerSerie',
                'createMethod' => 'createCommentaireSerie',
                'displayMethod' => 'afficherSerie'
            ]
        ];

        return $configs[$type] ?? throw new Exception("Type de commentaire invalide");
    }

    /**
     * @brief Redirige avec un message d'erreur
     */
    private function redirectWithError(string $type, string $idTmdb, string $message): void
    {
        $config = $this->getConfig($type);
        $controller = new $config['controller']($this->getTwig(), $this->getLoader());
        $_GET['tmdb_id'] = $idTmdb;
        $method = $config['displayMethod'];
        $controller->$method(['messagederreur' => $message]);
    }

    /**
     * @brief Redirige avec un message de succès
     */
    private function redirectWithSuccess(string $type, string $idTmdb): void
    {
        $config = $this->getConfig($type);
        $controller = new $config['controller']($this->getTwig(), $this->getLoader());
        $_GET['tmdb_id'] = $idTmdb;
        $method = $config['displayMethod'];
        $controller->$method(['message' => 'Votre commentaire a été ajouté avec succès.']);
    }
}
