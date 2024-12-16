<?php

/**
 * @brief Classe ControllerQuizz
 * 
 * @details Classe permettant de gérer les actions liées aux fils de discussion du forum
 * 
 * @date 29/11/2024
 * 
 * @version 1.0
 * 
 * @note Classe héritant de la classe Controller
 * 
 * @author Marylou Lohier
 */
class ControllerQuizz extends Controller {

    /**
     * @brief Constructeur de la classe ControllerQuizz
     * 
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Méthode de listing des quizz
     * 
     * @return void
     */
    public function listerQuizz(){
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quizz = $quizzDAO->findAll();

        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quizz' => $quizz
        ]);
    }

    /**
     * @brief Méthode d'affichage d'un quizz par son identifiant
     * 
     * @details Méthode permettant d'afficher un quizz par son identifiant, et ainsi de permettre l'afficahge de la discussion sous-jacente
     *
     * @return void
     */
    public function afficherQuizzParId(){
        $id = $_GET['idQuizz'];

        // Récupérer les infos du quizz
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quizz = $quizzDAO->findById($id);
        
        // Rendre le template avec les infos
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quizz' => $quizz
            
        ]);
    }

    public function jouerQuizz(){
        $id = $_GET['idQuizz'];
        //A faire

        // Récupérer les infos du quizz
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quizz = $quizzDAO->findById($id);
            
        // Rendre le template avec les infos
        echo $this->getTwig()->render('quizz.html.twig', [
            'quizz' => $quizz
                
        ]);
    }

    public function creerQuizz(){
        $idUtilisateur = $_GET['idUtilisateur']; 
        //A faire
        
        // Rendre le template avec les infos
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quizz' => $quizz
                
        ]);
    }

    public function gererQuizz(){
        listerQuizz();
    }
}