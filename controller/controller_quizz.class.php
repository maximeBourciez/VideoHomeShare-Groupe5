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

        if (isset($_SESSION['utilisateur'])){
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $pseudoUtilisateur = $utilisateur->getPseudo();
        }
        else{
            $pseudoUtilisateur = null;
        }
        //Génération de la vue
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quizz' => $quizz,
            'pseudoUtilisateur' => $pseudoUtilisateur
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
    public function afficherOngletGerer(): void
    {
        $managerQuizz = new QuizzDAO($this->getPdo());
        if (isset($_SESSION['utilisateur'])){
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();

            $quizz = $managerQuizz->findAllByUser($idUtilisateur);

            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur,
                'quizz' => $quizz
            ]);
        }
        else{
            //Récupération des quizz
            $quizz = $managerQuizz->findAll();
            
            //Génération de la vue
            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'quizz' => $quizz,
                'pseudoUtilisateur' => null,
                'messagederreur' => "Vous devez être connecté pour avoir accès à vos quizz."
            ]);
        }
    }

    /**
     * @brief Méthode qui permet de jouer au quizz
     * 
     * @details Méthode permettant d'afficher les questions et réponses du associés au quizz
     *
     * @return void
     */
    public function jouerQuizz() : void
    {
        if (isset($_SESSION['utilisateur'])){
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();

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

    /**
     * @brief Méthode qui retourne toutes les réponses d'une question
     * 
     * @details Méthode permettant de retourner toutes les réponses d'une question sous forme de tableau
     *
     * @return array
     */
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
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $idUtilisateur = $utilisateur->getId();

        echo $this->getTwig()->render('creationQuizz.html.twig', [
            'idUtilisateur' => $idUtilisateur
        ]);
    }

    /**
     * @brief Méthode permettant de créer une question
     * 
     * @details Méthode qui redirige l'utilisateur vers la page des questions pour créer ou modifier sa question
     *
     * @return array
     */
    public function creerQuestion() : void
    {
        //Récupération des informations
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $difficulte = $_POST['difficulte'];
        $nbQuestions = range(1,$_POST['nbQuestions']);
        $dateC = date('Y-m-d');
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $idUtilisateur = $utilisateur->getId();

        //Création de l'objet Quizz
        $managerQuizz = new QuizzDAO($this->getPdo());
        $managerQuizz->create($titre,$description,$difficulte,$dateC,$idUtilisateur);
        $newQuizz = $managerQuizz->findByTitreUser($titre,$idUtilisateur);
        $idQuizz = $newQuizz->getId();

        echo $this->getTwig()->render('creationQuestion.html.twig', [
            'idQuizz' => $idQuizz,
            'nbQuestions' => $nbQuestions,
            'idUtilisateur' => $idUtilisateur
        ]);
    }

    /**
     * @brief Méthode permettant d'afficher la page des résultats
     * 
     * @details Méthode qui redirige l'utilisateur vers la page des résultats
     *
     * @return void
     */
    public function afficherResultats(): void
    {
        //Récupération des infos
        $scoreUser = $_GET['bonnesReponses'];
        $idQuizz = $_GET['idQuizz'];
        $idUtilisateur = $_GET['idUtilisateur'];

        //Manipulation de l'objet Jouer
        $managerJouer = new JouerDAO($this->getPdo());
        if ($managerJouer->verifScoreUser($idQuizz, $idUtilisateur)){
            $newScore = new Jouer($idUtilisateur,$idQuizz,$scoreUser);
            $managerReponse->update($newScore);
        }
        else{
            $newScore = new Jouer($idUtilisateur,$idQuizz,$scoreUser);
            $managerReponse->create($newScore);
        }

        //Manipulation du tableau des scores
        $tabScores[] = $managerReponse->findAllByQuizz($idQuizz);
        //Trie le tableau par ordre décroissant de scores
        usort($tabScores, function($utilisateur1, $utilisateur2) {
            return $utilisateur2['score'] <=> $utilisateur1['score'];
        });
        $nbScores = range(1, sizeof($tabScores));


        echo $this->getTwig()->render('pageResultats.html.twig', [
            'idQuizz' => $idQuizz,
            'scores' => $tabScores,
            'nbScores' => $nbScores
        ]);
    }

}