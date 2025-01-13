<?php
/**
 * @brief Classe ControllerSuizz
 * 
 * @details Classe permettant de gérer les actions liées aux quizz
 * 
 * @date 11/1/2025
 * 
 * @version 2.0
 * 
 * @note Classe héritant de la classe Controller
 * 
 * @author Marylou Lohier
 */
class ControllerQuizz extends Controller
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
     * @bref permet d'afficher la page d'accueil des quizz
     *
     * @return void
     */
    public function listeQuizz(): void
    {
        //Recupération des quizz
        $managerQuizz = new QuizzDAO($this->getPdo());
        $quizz = $managerQuizz->findAll();

        //Génération de la vue
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
        $quiz = $quizzDAO->findById($idQuizz);
        
        // Rendre le template avec les infos
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quiz' => $quiz
            
        ]);
    }

    /**
     * @brief Méthode qui affiche les quizz de l'utilisateur connecté
     * 
     * @details Méthode permettant d'afficher les quizz de l'utilisateur connecté avec la possibilité de les gérer (modifier ou supprimer)
     *
     * @return void
     */
    public function gererQuizzUtilisateur(): void
    {
        if (isset($_SESSION['utilisateur'])){
            $idUtilisateur = unserialize($_SESSION['utilisateur']);
            $managerQuizz = new QuizzDAO($this->getPdo());
            $quizz = $managerQuizz->findAll();

            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur,
                'quizz' => $quizz
            ]);
        }
        else{
            echo "Vous devez être connecté pour avoir accès aux quizz. <br>";
            $this->listeQuizz();
        }
    }

    public function jouerQuizz() : void
    {
        if (isset($_SESSION['utilisateur'])){
            $idUtilisateur = unserialize($_SESSION['utilisateur']);

            $managerQuizz = new QuizzDAO($this->getPdo());
            $idQuizz = $_GET['idQuizz'];
            $quizz = $managerQuizz->find($idQuizz);

            $managerQuestion = new QuestionDAO($this->getPdo());
            $questions = $managerQuestion->findByQuizzId($idQuizz);

            $reponses = $this->reponsesDuneQuestion($questions);

            echo $this->getTwig()->render('participerQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur,
                'quizz' => $quizz,
                'questions' => $questions,
                'reponses' => $reponses
            ]);
        }
        else{
            echo "Vous devez être connecté pour pouvoir jouer aux quizz. <br>";
            $this->listeQuizz();
        }  
    }

    public function reponsesDuneQuestion(Question $questions) : array
    {
        $tabReponses = [];
        foreach ($questions as $question){
            $managerReponse = new ReponseDAO($this->getPdo());
            $tabReponses[] = $managerReponse->findByQuestionId($question.getIdQuestion());
        };

        return $tabReponses;
    }

    public function creerQuizz() : void
    {
        $idUtilisateur = $_GET['idUtilisateur']; 

        echo $this->getTwig()->render('creationQuizz.html.twig', [
            'idUtilisateur' => $idUtilisateur
        ]);
    }

    public function creerQuestion() : void
    {
        $idQuizz = $_GET['idQuizz'];

        echo $this->getTwig()->render('creationQuestion.html.twig', [
            'idQuizz' => $idQuizz
        ]);
    }

}