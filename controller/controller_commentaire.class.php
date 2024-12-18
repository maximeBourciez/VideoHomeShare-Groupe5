<?php

class ControllerCommentaire extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

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
