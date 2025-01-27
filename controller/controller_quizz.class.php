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
            //Récupération des quizz
            $managerQuizz = new QuizzDAO($this->getPdo());
            $quizz = $managerQuizz->findAll();
            
            //Génération de la vue
            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'quizz' => $quizz,
                'messagederreur' => "Vous devez être connecté pour pouvoir jouer à un quizz."
            ]); 
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

    public function afficherPageCreerQuizz() : void
    {
        if (isset($_SESSION['utilisateur'])){
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();

            echo $this->getTwig()->render('creationQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur
            ]);
        }
        else{
            //Récupération des quizz
            $managerQuizz = new QuizzDAO($this->getPdo());
            $quizz = $managerQuizz->findAll();
            
            //Génération de la vue
            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'quizz' => $quizz,
                'messagederreur' => "Vous devez être connecté pour pouvoir créer un quizz."
            ]);        

        }
    }

        /**
     * @brief Méthode permettant de créer un quizz
     * 
     * @details Méthode qui crée un quizz selon les informations entrées en paramètres
     *
     * @return int id du quizz créé
     */
    public function creerQuizz(string $titre, string $description, int $difficulte, string $dateC, string $idUtilisateur) : int
    {
        //Création de l'objet Quizz
        $managerQuizz = new QuizzDAO($this->getPdo());
        $idQuizz = $managerQuizz->create($titre,$description,$difficulte,$dateC,$idUtilisateur);

        return $idQuizz;
    }

    /**
     * @brief Méthode permettant d'afficher la page des questions après avoir créé le quizz
     * 
     * @details Méthode qui redirige l'utilisateur vers la page des questions pour créer ou modifier sa question
     *
     * @return void
     */
    public function afficherPageQuestions() : void
    {
        //Récupération des informations
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $difficulte = $_POST['difficulte'];
        $nbQuestions = $_POST['nbQuestions'];
        $numQuestion = $_GET['numQuestion'] ?? 1;
        $dateC = date('Y-m-d');

        $utilisateur = unserialize($_SESSION['utilisateur']);
        $idUtilisateur = $utilisateur->getId();

        $idQuizz = $this->creerQuizz($titre,$description,$difficulte,$dateC,$idUtilisateur);

        if ($numQuestion > $nbQuestions) {
            header('Location: index.php?controller=quizz&methode=listeQuizz' . $idQuizz);
            exit;
        }

        echo $this->getTwig()->render('creationQuestion.html.twig', [
            'idQuizz' => $idQuizz,
            'numQuestion' => $numQuestion,
            'nbQuestions' => $nbQuestions,
            'idUtilisateur' => $idUtilisateur
        ]);
    }

        /**
     * @brief Méthode permettant de créer les questions
     * 
     * @details Méthode qui créer les questions d'un quizz créé par l'utilisateur
     * @return void
     */
    public function creerQuestion() : void
    {
        //Récupération des informations
        $idQuizz = $_GET['idQuizz'];
        $numQuestion = intval($_GET['numQuestion']) ?? 1;
        $nbQuestions = intval($_GET['nbQuestions']);
        $idUtilisateur = $_GET['idUtilisateur'];

        //On vérifie que toutes les questions ont été créées
        if ($numQuestion > $nbQuestions) {
            header('Location: index.php?controller=quizz&methode=listeQuizz' . $idQuizz);
            exit;
        }

        echo $this->getTwig()->render('creationQuestion.html.twig', [
            'idQuizz' => $idQuizz,
            'numQuestion' => $numQuestion,
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