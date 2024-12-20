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
        $idQuizz = $_GET['idQuizz'];

        // Récupérer les infos du quizz
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quizz = $quizzDAO->findById($idQuizz);
        
        // Rendre le template avec les infos
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quizz' => $quizz
            
        ]);
    }

    /**
     * @brief Méthode d'affichage d'un quizz en mode jeu
     * 
     * @details Méthode permettant d'afficher un quizz en mode jeu, qui affiche chaque question avec les réponses correspondantes
     *
     * @return void
     */
    public function jouerQuizz(){
        $idQuizz = $_GET['idQuizz'];
        //A faire

        // Récupérer les infos du quizz
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quizz = $quizzDAO->findById($idQuizz);
            
        // Rendre le template avec les infos
        echo $this->getTwig()->render('jouerQuizz.html.twig', [
            'quizz' => $quizz
                
        ]);
    }

    /**
     * @brief Méthode de création d'un quizz
     * 
     * @details Méthode qui permet de créer ou de modifier un quizz en récupérant les valeurs nécessaires pour un quizz précis
     *
     * @return void
     */
    public function creerQuizz(){ 
        $idQuizz = $_GET['idQuizz'];
        $idUtilisateur = $_GET['idUtilisateur']; 

        if ($idQuizz !== NULL){
            // Rendre le template avec les infos
            echo $this->getTwig()->render('creationQuizz.html.twig', [
                'idQuizz' => $idQuizz, //Passe l'ID au template pour qu'il puisse être utilisé
                'idUtilisateur' => $idUtilisateur
            ]);
        }
        else{
            echo $this->getTwig()->render('creationQuizz.html.twig');
        }
    }

    /**
     * @brief Méthode de suppression d'un quizz
     * 
     * @details Méthode permettant de supprimer un quizz en récupérant son id
     *
     * @return void
     */
    public function supprimerQuizz(){
        $idQuizz = $_GET['idQuizz'];
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quizzDAO->delete($idQuizz);

        //Redirection
        listerQuizz();
    }

    /**
     * @brief Méthode permettant de gérer un quizz
     * 
     * @details Méthode permettant de gérer le quizz d'un utilisateur
     *
     * @return void
     */
    public function gererQuizz() : String{
        //A faire
    }

    /**
     * @brief Méthode de gestion de question
     * 
     * @details Méthode permettant de gérer une question et appelle l'interface correspondante
     *
     * @return void
     */
    public function gererQuestion(){
        //Récupère toutes les infos du quizz (à déplacer)
        $idQuizz = $_POST['idQuizz'];
        $titre = $_POST['titre'];
        $desc = $_POST['desc'];
        $difficulte = $_POST['difficulte'];
        $nbQuestions = $_POST['nbQuestions'];
            
        if ($idQuizz !== NULL){
            // Récupérer les infos du quizz
            $quizzDAO = new QuizzDAO($this->getPdo());
            $quizz = $quizzDAO->findById($idQuizz);
            // Rendre le template avec les infos
            echo $this->getTwig()->render('creationQuestion.html.twig', [
                'quizz' => $quizz
            ]);
        }
        else{
            //Crée un nouveau quizz (à déplacer)
            $quizz = new Quizz($idQuizz, $titre, $desc, $difficulte, $nbQuestions);

            $quizzDAO = new QuizzDAO($this->getPdo());
            $quizz = $quizzDAO->findById($idQuizz);
            
            // Rendre le template sans information
            echo $this->getTwig()->render('creationQuestion.html.twig');
        }       
    }

}