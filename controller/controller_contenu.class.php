<?php
/**
 * @brief Classe ControllerContenu
 * 
 * @details Classe permettant de gérer le contenu du site
 * 
 * @date 5/11/2024
 * 
 * @warning Cette classe met en place une version simplifiée du design pattern Factory
 */
class ControllerContenu extends Controller {
    /**
     * @brief Constructeur de la classe ControllerContenu
     * 
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    public function afficherFilm(){
        $id = isset($_GET['id_film']) ? $_GET['id_film'] : null;

        // Récupération des informations du film (contenu)
        $managerContenu = new ContenuDAO($this->getPdo());
        $contenu = $managerContenu->findById($id);

        // Récupération des thèmes associés au contenu
        $managerTheme = new ThemeDAO($this->getPdo());
        $themes = $managerTheme->findThemesByContenuId($id);
        
        // Récupérer la moyenne des notes et le total des commentaires pour ce contenu
        $managerCommentaireMoy = new CommentaireDAO($this->getPdo());
        $notes = $managerCommentaireMoy->getMoyenneEtTotalNotesContenu($id); // Retourne un tableau avec 'moyenne' et 'total'

        // Récupérer les commentaires spécifiques à ce contenu
        $managerCommentaire = new CommentaireDAO($this->getPdo());
        $commentaires = $managerCommentaire->getCommentairesContenu($id);

        // Récupérer les personnalités associées à ce contenu
        $managerPersonnalite = new PersonnaliteDAO($this->getPdo());
        $personnalite = $managerPersonnalite->findAllParContenuId($id); 

        // Rendu du template avec toutes les données récupérées
        echo $this->getTwig()->render('pageDunFilm.html.twig', [
            'contenu' => $contenu,
            'themes' => $themes,
            'moyenne' => $notes['moyenne'],
            'total' => $notes['total'],
            'personnalite' => $personnalite,
            'commentaires' => $commentaires // Changement ici
        ]);
    }
}


?>