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
        $id = $_GET['id_film'];
    
        $managerContenu = new ContenuDAO($this->getPdo());
        $contenu = $managerContenu->findById($id);
    
        $managerTheme = new ThemeDAO($this->getPdo());
        $themes = $managerTheme->findThemesByContenuId($id);
        
        $managerCommentaire = new CommentaireDAO($this->getPdo());
        $notes = $managerCommentaire->getMoyenneEtTotalNotesContenu($id); // Retourne un tableau avec 'moyenne' et 'total'

        $managerPersonnalite = new PersonnaliteDAO($this->getPdo());
        $personnalite = $managerPersonnalite->findAllParContenuId($id); 

        echo $this->getTwig()->render('pageDunFilm.html.twig', [
            'contenu' => $contenu,
            'themes' => $themes,
            'moyenne' => $notes['moyenne'],
            'total' => $notes['total'],
            'personnalite' => $personnalite
        ]);
    }
 }