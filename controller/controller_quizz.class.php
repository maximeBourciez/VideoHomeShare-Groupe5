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
     * @param string|null $message Message à afficher
     * @param bool|null $etatMessage Etat du message (true = succès, false = erreur)
     *
     * @return void
     */
    public function listeQuizz(?string $message = null, ?bool $etatMessage = null): void
    {
        //Recupération des quizz
        $managerQuizz = new QuizzDAO($this->getPdo());
        $quizz = $managerQuizz->findAll();

        // Vérifier si l'utilisateur est connecté
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $pseudoUtilisateur = $utilisateur->getPseudo();
        } else {
            $pseudoUtilisateur = null;
        }

        // Initialisation des messages
        $messageConfirmation = null;
        $messagederreur = null;

        if ($message !== null) {
            if ($etatMessage) {
                echo $this->getTwig()->render('listeQuizz.html.twig', [
                    'quizz' => $quizz,
                    'boutonGererAppuye' => false,
                    'boutonVoirAppuye' => true,
                    'pseudoUtilisateur' => $pseudoUtilisateur,
                    'messageConfirmation' => $message
                ]);
                exit();
            } else {
                echo $this->getTwig()->render('listeQuizz.html.twig', [
                    'quizz' => $quizz,
                    'boutonGererAppuye' => false,
                    'boutonVoirAppuye' => true,
                    'pseudoUtilisateur' => $pseudoUtilisateur,
                    'messagederreur' => $message
                ]);
                exit();
            }
        }

        // Génération de la vue
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quizz' => $quizz,
            'boutonGererAppuye' => false,
            'boutonVoirAppuye' => true,
            'pseudoUtilisateur' => $pseudoUtilisateur
        ]);
        exit();
    }

    /**
     * @brief Méthode qui affiche les quizz de l'utilisateur connecté
     * 
     * @details Méthode permettant d'afficher les quizz de l'utilisateur connecté avec la possibilité de les supprimer
     *
     * @return void
     */
    public function afficherOngletGerer(): void
    {
        $managerQuizz = new QuizzDAO($this->getPdo());

        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();
            $pseudoUtilisateur = $utilisateur->getPseudo();

            $quizz = $managerQuizz->findAllByUser($idUtilisateur);

            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur,
                'pseudoUtilisateur' => $pseudoUtilisateur,
                'boutonGererAppuye' => true,
                'quizz' => $quizz
            ]);
        } else {
            //Récupération des quizz
            $quizz = $managerQuizz->findAll();

            //Génération de la vue
            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'quizz' => $quizz,
                'pseudoUtilisateur' => null,
                'boutonGererAppuye' => false,
                'messagederreur' => "Vous devez être connecté pour avoir accès à vos quizz."
            ]);
        }
    }

    /**
     * @brief Méthode qui permet de récupérer la question et ses réponses envoyés vers la page
     * 
     * @details Méthode permettant d'afficher la question et réponses associés au quizz
     * @param $idQuestion
     * @return array
     */
    public function recupererReponses(int $idQuestion): array
    {
        $reponses = [];
        $managerReponse = new ReponseDAO($this->getPdo());
        $reponses = $managerReponse->findAllByQuestionId($idQuestion);

        return $reponses;
    }

    public function verifierReponses(int $nbBonnesReponses, array $reponses) : int
    {
        foreach ($reponses as $reponse){
            $rangReponse = $reponse->getRang();
            if (isset($_POST['reponse_' . $rangReponse]) && $reponse->getVerite()){
                $nbBonnesReponses++;
            }
        }

        return $nbBonnesReponses;
    }

    /**
     * @brief Méthode qui permet de vérifier si la bonne réponse a été cochée qui renvoie vers la question suivante
     * 
     * @details Méthode qui récupère la question et les réponses, accorde ou non le point puis renvoie vers la page avec la question suivante
     *
     * @return void
     */
    public function traiterResultatsQuestion() : void
    {
        //Récupérer les informations
        $nbBonnesReponses = intval($_POST['nbBonnesReponses']);
        $idQuizz = intval($_POST['idQuizz']);
        $idQuestion = intval($_POST['idQuestion']);
        $idUtilisateur = $_POST['idUtilisateur'];
        $derniereQuestion = $_POST['derniereQuestion'];

        //Récupérer le quizz et ses questions
        $managerQuizz = new QuizzDAO($this->getPdo());
        $quizz = $managerQuizz->find($idQuizz);
        $managerQuestion = new QuestionDAO($this->getPdo());
        $questions = $managerQuestion->findByQuizzId($idQuizz);        

        //Récupérer les réponses et compter le nombre de bonnes réponses
        $reponses = $this->recupererReponses($idQuestion);
        $nbBonnesReponses = $this->verifierReponses($nbBonnesReponses, $reponses);

        //Afficher les résultats
        if ($derniereQuestion) { //Si c'était déjà à true, on affiche les résultats
            $this->afficherResultats($idUtilisateur, $nbBonnesReponses, $idQuizz);
            exit();
        }

        //Incrémenter l'id de la question pour passer à la question suivante
        $idQuestion++;
        //Récupérer la question et son rang
        $question = $managerQuestion->find($idQuestion);
        $rangQuestion = $question->getRang();

        //Si le rang de la question est égale au nombre de questions => la question actuelle est la dernière
        if ($rangQuestion == sizeof($questions)) {
            $derniereQuestion = true;
        }
        
        //Récupérer les réponses de la prochaine question
        $reponses = $this->recupererReponses($idQuestion);

        echo $this->getTwig()->render('participerQuizz.html.twig', [
            'idUtilisateur' => $idUtilisateur,
            'quizz' => $quizz,
            'question' => $question,
            'reponses' => $reponses,
            'derniereQuestion' => $derniereQuestion,
            'nbBonnesReponses' => $nbBonnesReponses
        ]);
        exit();
    }

    /**
     * @brief Méthode qui permet de jouer au quizz
     * 
     * @details Méthode permettant d'afficher les questions et réponses associés au quizz
     *
     * @return void
     */
    public function jouerQuizz(): void
    {
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();
            $pseudoUtilisateur = $utilisateur->getPseudo(); //Utile quand le quizz n'a pas de question

            //Récupération des infos du quizz
            $managerQuizz = new QuizzDAO($this->getPdo());
            $idQuizz = intval($_POST['idQuizz']);
            $quizz = $managerQuizz->find($idQuizz);

            //Variable pour savoir si c'est la dernière question
            $derniereQuestion = false; //false par défaut

            //Récupération de la 1re question
            $managerQuestion = new QuestionDAO($this->getPdo());
            $questions = $managerQuestion->findByQuizzId($idQuizz);

            //Si le quizz sélectionné avait été créé par erreur par son auteur, on renvoie un message d'erreur
            if ($questions == null){
                $quizz = $managerQuizz->findAll();
                echo $this->getTwig()->render('listeQuizz.html.twig', [
                    'quizz' => $quizz,
                    'boutonGererAppuye' => false,
                    'boutonVoirAppuye' => true,
                    'pseudoUtilisateur' => $pseudoUtilisateur,
                    'messagederreur' => "Quizz indisponible, veuillez en sélectionner un autre."
                ]);

                exit();
            }

            $question = $questions[0]; //Sinon on se place sur la 1re question

            //Tableau des réponses de quizz
            $reponses = [];

            $idQuestion = $question->getIdQuestion();
            $reponses = $this->recupererReponses($idQuestion);

            //Vérifier que la question soit la dernière
            if ($question->getRang() == sizeof($questions)){
                $derniereQuestion = true;
            }

            echo $this->getTwig()->render('participerQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur,
                'quizz' => $quizz,
                'question' => $question,
                'reponses' => $reponses,
                'derniereQuestion' => $derniereQuestion,
                'nbBonnesReponses' => 0
            ]);
            exit();
        } else {
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

    public function afficherPageCreerQuizz(): void
    {
        if (isset($_SESSION['utilisateur'])) {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();

            echo $this->getTwig()->render('creationQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur
            ]);
        } else {
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
     * @brief Méthode permettant de supprimer un quizz
     * 
     * @details Méthode qui supprime un quizz de la liste et de la base de données
     *
     * @return void Message de confirmation ou d'erreur dans le template
     */
    public function supprimerQuizz(): void
    {
        // Vérifier que la méthode est POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=quizz&methode=afficherOngletGerer');
            exit();
        }

        // Vérifier la connexion
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?controller=utilisateur&methode=connexion');
            exit();
        }

        // Récupérer l'ID du quizz
        $idQuizz = $_POST['idQuizz'];

        // Supprimer le quizz 
        $managerQuizz = new QuizzDAO($this->getPdo());
        $supprOk = $managerQuizz->delete($idQuizz);

        // Vérifier si la suppression a fonctionné
        if ($supprOk) {
            $this->listeQuizz("Le quizz a bien été supprimé.", true);
            exit();
        } else {
            echo $this->getTwig()->render('listeQuizz.html.twig', [
                'messageErreur' => "Une erreur est survenue lors de la suppression du quizz."
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
    public function creerQuizz(string $titre, string $description, int $difficulte, string $dateC, string $idUtilisateur): int
    {
        //Création de l'objet Quizz
        $managerQuizz = new QuizzDAO($this->getPdo($this->getPdo()));
        $idQuizz = $managerQuizz->create($titre, $description, $difficulte, $dateC, $idUtilisateur);

        return $idQuizz;
    }

    /**
     * @brief Méthode permettant d'afficher la page des questions après avoir créé le quizz
     * 
     * @details Méthode qui redirige l'utilisateur vers la page des questions pour créer sa question
     *
     * @return void
     */
    public function afficherPageQuestions(?string $messageErreur = null): void
    {
        //Récupération des informations
        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $difficulte = htmlspecialchars($_POST['difficulte']);
        $nbQuestions = htmlspecialchars($_POST['nbQuestions']);
        $numQuestion = 1;
        $dateC = date('Y-m-d');

        $utilisateur = unserialize($_SESSION['utilisateur']);
        $idUtilisateur = $utilisateur->getId();

        $idQuizz = $this->creerQuizz($titre, $description, $difficulte, $dateC, $idUtilisateur);

        echo $this->getTwig()->render('creationQuestion.html.twig', [
            'idQuizz' => $idQuizz,
            'numQuestion' => $numQuestion,
            'nbQuestions' => $nbQuestions,
            'idUtilisateur' => $idUtilisateur,
            'messageErreur' => $messageErreur
        ]);
        exit();
    }


    /**
     * @brief Méthode permettant de créer les questions
     * 
     * @details Méthode qui crée une question d'un quizz créé par l'utilisateur
     * 
     * @return void
     * 
     * @todo Gerer le succès de fin de quizz 
     */
    public function ajouterQuestion(): void
    {
        // Vérifier la méthode 
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?controller=quizz&methode=afficherPageQuestions');
            exit();
        }

        // Vérifier la connexion
        if (!isset($_SESSION['utilisateur'])) {
            header('Location: index.php?controller=utilisateur&methode=connexion');
            exit();
        } else {
            $utilisateur = unserialize($_SESSION['utilisateur']);
            $idUtilisateur = $utilisateur->getId();
        }

        try {

            // Récupérer les données
            $numQuestion = $_POST['numQuestion'];
            $idQuizz = $_POST['idQuizz'];
            $titre = $_POST['titre_' . $numQuestion];
            $nbQuestions = $_POST['nbQuestions'];
            $imageKey = "image_" . $numQuestion;
            $imagePath = __DIR__ .'/../images/quizz/default.jpg'; // Valeur par défaut

            // Vérifier et traiter l'image si elle existe
            if (isset($_FILES[$imageKey]) && $_FILES[$imageKey]['error'] === UPLOAD_ERR_OK) {
                $image = $_FILES[$imageKey];
                $imagePath = __DIR__ . '/../images/quizz/' . basename($image['name']);
                
                if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                    throw new Exception("Erreur lors du téléchargement de l'image.");
                }
            } 
            // On continue même si pas d'image (utilisation de l'image par défaut)

            // Vérifier qu'au moins une réponse est correcte
            $auMoinsUneReponseCorrecte = false;
            for ($i = 0; $i < 4; $i++) {
                if (isset($_POST["correcte_{$numQuestion}_{$i}"])) {
                    $auMoinsUneReponseCorrecte = true;
                    break;
                }
            }

            if (!$auMoinsUneReponseCorrecte) {
                throw new Exception("Vous devez sélectionner au moins une réponse correcte.");
            }

            // Créer la question
            $managerQuestion = new QuestionDAO($this->getPdo());
            $idQuestion = $managerQuestion->create($titre, $numQuestion, $imagePath, $idQuizz);

            // Vérifier que l'ID de la question a bien été récupéré
            if (!$idQuestion) {
                throw new Exception("Erreur lors de la création de la question.");
            }

            // Créer les réponses
            $managerReponses = new ReponseDAO($this->getPdo());
            for ($i = 0; $i < 4; $i++) {
                $reponseKey = "reponse_{$numQuestion}_{$i}";
                $correcteKey = "correcte_{$numQuestion}_{$i}";

                if (isset($_POST[$reponseKey])) {
                    $reponse = trim($_POST[$reponseKey]);
                    $estCorrecte = isset($_POST[$correcteKey]);

                    $objetReponse = new Reponse(0, $reponse, $i + 1, $estCorrecte, $idQuestion);
                    if (!$managerReponses->create($objetReponse)) {
                        throw new Exception("Erreur lors de la création d'une réponse.");
                    }
                }
            }

            // Vérifier si on a encore des questions à traiter
            if ($numQuestion < $nbQuestions) {
                $numQuestion++;
                echo $this->getTwig()->render('creationQuestion.html.twig', [
                    'idQuizz' => $idQuizz,
                    'numQuestion' => $numQuestion,
                    'nbQuestions' => $nbQuestions,
                    'idUtilisateur' => $idUtilisateur
                ]);
            } else {
                header('Location: index.php?controller=quizz&methode=afficherOngletGerer');
            }
            exit();

        } catch (Exception $e) {
            // Afficher la page avec le message d'erreur
            echo $this->getTwig()->render('creationQuestion.html.twig', [
                'idQuizz' => $idQuizz,
                'numQuestion' => $numQuestion,
                'nbQuestions' => $nbQuestions,
                'idUtilisateur' => $idUtilisateur,
                'messageErreur' => $e->getMessage()
            ]);
            exit();
        }
    }

    /**
     * @brief Méthode permettant d'afficher la page des résultats
     * 
     * @details Méthode qui redirige l'utilisateur vers la page des résultats
     *
     * @return void
     */
    public function afficherResultats(string $idUtilisateur, int $scoreUser, int $idQuizz): void
    {
        //Création du tableau des scores
        $tabScores = [];

        //Manipulation de l'objet Jouer
        $managerJouer = new JouerDAO($this->getPdo());

        if ($managerJouer->verifScoreExistant($idQuizz, $idUtilisateur)){
            if (!$this->memesScores($idUtilisateur, $idQuizz, $scoreUser)){
                //Si les scores sont différents, alors on update le score sinon non
                $managerJouer->update($idUtilisateur,$idQuizz,$scoreUser);
            }
        }
        else{
            $managerJouer->create($idUtilisateur,$idQuizz,$scoreUser);
        }

        //Manipulation du tableau des scores
        $tabScores = $managerJouer->findAllByQuizz($idQuizz);
        //Récupération des infos du quizz
        $managerQuizz = new QuizzDAO($this->getPdo());
        $quizz = $managerQuizz->find($idQuizz);
        //Récupérer l'utilisateur pour récupérer sa photo de profil
        //A réaliser

        echo $this->getTwig()->render('pageResultats.html.twig', [
            'scores' => $tabScores,
            'quizz' => $quizz
        ]);
    }

    /**
     * @brief Méthode qui vérifie 2 scores d'un utilisateur sur un quizz
     * 
     * @details Méthode qui prend en paramètre un quizz, un score et un utilisateur, puis compare les deux scores
     * @return bool
     */
    public function memesScores(string $idUtilisateur, int $idQuizz, int $scoreUser) : bool
    {
        $managerJouer = new JouerDAO($this->getPdo());
        $jouer = $managerJouer->findByQuizzUser($idQuizz, $idUtilisateur);

        if ($jouer && $jouer->getScore() == $scoreUser){
            return true;
        }

        return false;
    }
}