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
        $idTmdbContenu = $_POST['idContenu'];
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $idUtilisateur = $utilisateur->getId();
        
        $estPositif = $_POST['note'] >= 3 ? true : false;

        $commentaire = new Commentaire(
            $idUtilisateur,
            $_POST['titre'],
            $_POST['note'],
            $_POST['commentaire'],
            $estPositif,
            $idTmdbContenu
        );

        $commentaireDAO = new CommentaireDAO($this->getPdo());
        $commentaireDAO->createCommentaireContenu($commentaire);

        // Créer une instance du ControllerContenu
        $controllerContenu = new ControllerContenu($this->getTwig(), $this->getLoader());
        
        // Définir l'ID TMDB dans $_GET pour la méthode afficherContenu
        $_GET['tmdb_id'] = $idTmdbContenu;
        
        // Appeler directement la méthode d'affichage
        $controllerContenu->afficherContenu();
    }
}
