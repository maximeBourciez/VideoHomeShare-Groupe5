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
     * @brief Méthode d'affichage d'un quizz par son identifiant
     * 
     * @details Méthode permettant d'afficher un quizz par son identifiant, et ainsi de permettre l'afficahge de la discussion sous-jacente
     *
     * @return void
     */
    public function afficherQuizzParId()
    {
        $idQuizz = $_GET['idQuizz'];

        // Récupérer les infos du quizz
        $quizzDAO = new QuizzDAO($this->getPdo());
        $quiz = $quizzDAO->find($idQuizz);

        // Rendre le template avec les infos
        echo $this->getTwig()->render('listeQuizz.html.twig', [
            'quiz' => $quiz

        ]);
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
        $reponses = $managerReponse->findByQuestionId($idQuestion);

        return $reponses;
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

            //Récupération du quizz par son ID
            $managerQuizz = new QuizzDAO($this->getPdo());
            $idQuizz = $_GET['idQuizz'];
            $quizz = $managerQuizz->find($idQuizz);

            //Récupération des questions
            $managerQuestion = new QuestionDAO($this->getPdo());
            $questions = $managerQuestion->findByQuizzId($idQuizz);

            //Tableau des réponses de quizz
            $reponses = [];

            foreach ($questions as $question) {
                $idQuestion = $question->getIdQuestion();
                $reponses[$idQuestion] = $this->recupererReponses($idQuestion);
            }

            echo $this->getTwig()->render('participerQuizz.html.twig', [
                'idUtilisateur' => $idUtilisateur,
                'quizz' => $quizz,
                'questions' => $questions,
                'reponses' => $reponses
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
     * @brief Méthode permettant de supprimer les questions d'un quizz
     * 
     * @details Méthode qui supprime toutes les questions d'un quizz
     *
     * @return void
     */
    public function supprimerQuestions(int $idQuizz): void
    {
        $managerQuestion = new QuestionDAO($this->getPdo());
        $questions = $managerQuestion->findByQuizzId($idQuizz);

        $managerReponse = new ReponseDAO($this->getPdo());

        //Variables nécessaires
        $i = 0;

        foreach ($questions as $question) {
            $idQuestion = $question->getIdQuestion();
            $reponses = $managerReponse->findByQuestionId($idQuestion);
            foreach ($reponses as $reponse) {
                $managerReponse->delete($reponse->getId());
            }
            $managerQuestion->delete($idQuestion);

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
        $managerQuizz = new QuizzDAO($this->getPdo());
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
    public function afficherResultats(): void
    {
        //Récupération des infos
        $scoreUser = $_GET['bonnesReponses'];
        $idQuizz = $_GET['idQuizz'];
        $idUtilisateur = $_GET['idUtilisateur'];

        //Manipulation de l'objet Jouer
        $managerJouer = new JouerDAO($this->getPdo());
        if ($managerJouer->verifScoreUser($idQuizz, $idUtilisateur)) {
            $newScore = new Jouer($idUtilisateur, $idQuizz, $scoreUser);
            $managerReponse->update($newScore);
        } else {
            $newScore = new Jouer($idUtilisateur, $idQuizz, $scoreUser);
            $managerReponse->create($newScore);
        }

        //Manipulation du tableau des scores
        $tabScores[] = $managerReponse->findAllByQuizz($idQuizz);
        //Trie le tableau par ordre décroissant de scores
        usort($tabScores, function ($utilisateur1, $utilisateur2) {
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