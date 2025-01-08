<?php

/**
 * @brief Contrôleur gérant les commentaires
 * 
 * Cette classe gère la création et la gestion des commentaires sur les contenus
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class ControllerCommentaire extends Controller {
    /**
     * @brief Constructeur du contrôleur de commentaires
     * 
     * @param \Twig\Environment $twig L'environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de fichiers Twig
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Crée un nouveau commentaire pour un contenu
     * 
     * Cette méthode traite la soumission d'un nouveau commentaire :
     * - Récupère les données du formulaire
     * - Crée un nouvel objet Commentaire
     * - Sauvegarde le commentaire dans la base de données
     * - Redirige vers la page du contenu
     * 
     * @return void
     */
    public function createCommentaireContenu(): void {
        // Vérification de la session et désérialisation de l'utilisateur
        $utilisateur = isset($_SESSION['utilisateur']) ? unserialize($_SESSION['utilisateur']) : null;
        
        if (!$utilisateur) {
            $template = $this->getTwig()->load('pageDunContenu.html.twig');
            echo $template->render(['messagederreur' => 'Vous devez être connecté pour poster un commentaire.']);
            return;
        }

        // Récupération et nettoyage des données POST
        $idTmdbContenu = isset($_POST['idContenu']) ? htmlspecialchars($_POST['idContenu']) : null;
        $titre = isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : null;
        $note = isset($_POST['note']) ? (int)htmlspecialchars($_POST['note']) : null;
        $commentaireTexte = isset($_POST['commentaire']) ? htmlspecialchars($_POST['commentaire']) : null;

        // Validation des données
        $message = "";
        if (!Utilitaires::comprisEntre($titre, 100, 3, "Le titre doit contenir", $message) ||
            !Utilitaires::comprisEntre($commentaireTexte, 1000, 10, "Le commentaire doit contenir", $message)) {
            
            // Redirection vers la page du film avec message d'erreur
            $controllerContenu = new ControllerContenu($this->getTwig(), $this->getLoader());
            $_GET['tmdb_id'] = $idTmdbContenu;
            $controllerContenu->afficherContenu(['messagederreur' => $message]);
            return;
        }

        try {
            // Vérification des données avant la création de l'objet
            echo '<pre>';
            echo "ID Utilisateur: " . $utilisateur->getId() . "\n";
            echo "Titre: " . $titre . "\n";
            echo "Note: " . $note . "\n";
            echo "Texte du commentaire: " . $commentaireTexte . "\n";
            echo '</pre>';

            // Création de l'objet commentaire
            $commentaire = new Commentaire(
                $utilisateur->getId(),
                $titre,
                $note,
                $commentaireTexte,
                $note >= 3,
                $idTmdbContenu
            );

            // Affichage du contenu de l'objet commentaire
            echo '<pre>';
            print_r($commentaire);
            echo '</pre>';

            // Sauvegarde dans la base de données
            $commentaireDAO = new CommentaireDAO($this->getPdo());
            $commentaireDAO->createCommentaireContenu($commentaire);

            // Redirection vers la page du contenu avec message de succès
            $controllerContenu = new ControllerContenu($this->getTwig(), $this->getLoader());
            $_GET['tmdb_id'] = $idTmdbContenu;
            $controllerContenu->afficherContenu(['message' => 'Votre commentaire a été ajouté avec succès.']);

        } catch (Exception $e) {
            // Redirection vers la page du film avec message d'erreur
            $controllerContenu = new ControllerContenu($this->getTwig(), $this->getLoader());
            $_GET['tmdb_id'] = $idTmdbContenu;
            $controllerContenu->afficherContenu(['messagederreur' => $e->getMessage()]);
        }
    }

    /**
     * @brief Crée un nouveau commentaire pour une collection
     * 
     * Cette méthode traite la soumission d'un nouveau commentaire :
     * - Récupère les données du formulaire
     * - Crée un nouvel objet Commentaire
     * - Sauvegarde le commentaire dans la base de données
     * - Redirige vers la page de la collection
     * 
     * @return void
     */
    public function createCommentaireCollection(): void {
        // Vérification de la session et désérialisation de l'utilisateur
        $utilisateur = isset($_SESSION['utilisateur']) ? unserialize($_SESSION['utilisateur']) : null;
        
        if (!$utilisateur) {
            $template = $this->getTwig()->load('pageDuneCollection.html.twig');
            echo $template->render(['messagederreur' => 'Vous devez être connecté pour poster un commentaire.']);
            return;
        }

        // Récupération et nettoyage des données POST
        $idTmdbCollection = isset($_POST['idCollection']) ? htmlspecialchars($_POST['idCollection']) : null;
        $titre = isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : null;
        $note = isset($_POST['note']) ? (int)htmlspecialchars($_POST['note']) : null;
        $commentaireTexte = isset($_POST['commentaire']) ? htmlspecialchars($_POST['commentaire']) : null;

        // Validation des données
        $message = "";
        if (!Utilitaires::comprisEntre($titre, 100, 3, "Le titre doit contenir", $message) ||
            !Utilitaires::comprisEntre($commentaireTexte, 1000, 10, "Le commentaire doit contenir", $message)) {
            
            // Redirection vers la page du film avec message d'erreur
            $controllerCollection = new ControllerCollection($this->getTwig(), $this->getLoader());
            $_GET['tmdb_id'] = $idTmdbCollection;
            $controllerCollection->afficherCollection(['messagederreur' => $message]);
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
                $idTmdbCollection
            );

            // Affichage du contenu de l'objet commentaire
            echo '<pre>';
            print_r($commentaire);
            echo '</pre>';

            // Sauvegarde dans la base de données
            $commentaireDAO = new CommentaireDAO($this->getPdo());
            $commentaireDAO->createCommentaireCollection($commentaire);

            // Vérification de l'insertion
            if (!$commentaireDAO->createCommentaireCollection($commentaire)) {
                throw new Exception("Erreur lors de l'insertion du commentaire.");
            }

            // Redirection vers la page de la collection avec message de succès
            $controllerCollection = new ControllerCollection($this->getTwig(), $this->getLoader());
            $_GET['tmdb_id'] = $idTmdbCollection;
            $controllerCollection->afficherCollection(['message' => 'Votre commentaire a été ajouté avec succès.']);

        } catch (Exception $e) {
            // Redirection vers la page de la collection avec message d'erreur
            $controllerCollection = new ControllerCollection($this->getTwig(), $this->getLoader());
            $_GET['tmdb_id'] = $idTmdbCollection;
            $controllerCollection->afficherCollection(['messagederreur' => $e->getMessage()]);
        }
    }
}
