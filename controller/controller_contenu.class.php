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

        $contenuDAO = new ContenuDAO($this->getPdo());
        $contenu = $contenuDAO->findById($id);

        $themeDAO = new ThemeDAO($this->getPdo());
        $themes = $themeDAO->findThemesByContenuId($id);
        
        $noteDao = new CommentaireDAO($this->getPdo());
        $moyenne = $noteDao->getMoyenneNoteContenu($id);

        echo $this->getTwig()->render('pageDunFilm.html.twig', [
            'contenu' => $contenu,
            'themes' => $themes,
            'moyenne' => $moyenne
        ]);
    }
}