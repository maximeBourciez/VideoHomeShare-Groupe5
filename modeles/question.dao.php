<?php
/**
 * @file question.dao.php
 * 
 * @brief Classe QuestionDAO
 * 
 * @details Classe permettant de gérer les accès à la base de données pour les questions
 * 
 * @date 11/1/2025
 * 
 * @version 2.0
 * 
 * @author Marylou Lohier
 */
class QuestionDAO{
    // Attributs
    /**
     * @var PDO|null $pdo Connexion à la base de données
     */
    private $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe QuestionDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getter
    /**
     * @brief Getter de la connexion à la base de données
     * @return PDO|null Connexion à la base de données
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    // Setter
    /**
     * @brief Setter de la connexion à la base de données
     * @param PDO $pdo Connexion à la base de données
     * @return self
     */
    public function setPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }

    // Méthodes
    /**
     * @brief Méthode de création d'un quizz
     * 
     * @param string $valeur Valeur de la question
     * @param int $rang Rang de la question
     * @param string $urlImage URL de l'image de la question
     * @param int $idQuizz Identifiant du quizz 
     * 
     * @return int Confirmation ou non que la question a bien été créée
     */
    function create(string $valeur, int $rang, string $urlImage, int $idQuizz): int{
        // Préparer la requête
        $req = $this->pdo->prepare("INSERT INTO " . DB_PREFIX ."question (valeur, rang, urlImage, idQuizz) VALUES (:valeur, :rang, :urlImage, :idQuizz)");
        $req->bindParam(":valeur", $valeur, PDO::PARAM_STR);
        $req->bindParam(":rang", $rang, PDO::PARAM_INT);
        $req->bindParam(":urlImage", $urlImage, PDO::PARAM_STR);
        $req->bindParam(":idQuizz", $idQuizz, PDO::PARAM_INT);

        // Exécuter la requête
        $req->execute();

        // Retourner 
        return $this->pdo->lastInsertId();
    }

    /**
     * @brief Méthode de mise à jour d'un quizz
     * 
     * @param Question
     * @return bool
     */
    function update(Question $question): bool{
        $req = $this->pdo->prepare("UPDATE Question SET idQuestion = :idQuestion, valeur = :valeur, rang = :rang, urlImage = :urlImage, idQuizz = :idQuizz WHERE idQuestion = :idQuestion, idQuizz = :idQuizz");
        $req->bindParam(":idQuestion", $question->getIdQuestion());
        $req->bindParam(":valeur", $question->getValeur());
        $req->bindParam(":rang", $question->getRang());
        $req->bindParam(":urlImage", $question->getUrlImage());
        $req->bindParam(":idQuizz", $question->getIdQuizz());

        return $req->execute();
    }

    /**
     * @brief Méthode de suppression d'un quizz
     * 
     * @param idQuestion //identifiant du quizz
     * @return bool
     */
    function delete(int $idQuestion): bool{
        $req = $this->pdo->prepare("DELETE FROM Question WHERE idQuestion = :idQuestion");
        $req->bindParam(":idQuestion", $idQuestion);

        return $req->execute();
    }

    /**
     * @brief Méthode d'hydratation d'un quizz
     * 
     * @param array //tableau des attributs du quizz
     * @return Question
     */
    function hydrate(array $row): Question{
        // Récupération des valeurs
        $idQuestion = $row['idQuestion'];
        $valeur = $row['valeur'];
        $rang = $row['rang'];
        $urlImage = $row['urlImage'];
        $idQuizz = $row['idQuizz'];

        // Retourner la Question
        return new Question($idQuestion, $valeur, $rang, $urlImage, $idQuizz);
    }

    /**
     * @brief Méthode d'hydratation de toutes les questions
     * 
     * @param array //tableau des attributs des questions
     * @return array
     */
    function hydrateAll(array $rows): array{
        $questions = [];
        foreach($rows as $row){
            $question = $this->hydrate($row);
            array_push($questions, $question);  // Ajout de la question au tableau 
        }
        return $questions;
    }

    /**
     * @brief Méthode pour récupérer les attributs d'une question
     * 
     * @param $idQuestion //identifiant de la question
     * @return Question
     */
    function find(int $idQuestion): ?Question{
        $sql = "SELECT * FROM " . DB_PREFIX . "question WHERE idQuestion = :idQuestion";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idQuestion', $idQuestion, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == null){
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * @brief Méthode pour récupérer les attributs des quizz
     * 
     * @return array
     */
    function findAll(): array{
        $sql = "SELECT * FROM " .DB_PREFIX. "question";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Méthode pour récupérer les questions d'un quizz
     * @param $idQuizz identifiant du quizz
     * @return array
     */
    function findByQuizzId(int $idQuizz): ?array{
        $sql = "SELECT *
        FROM " .DB_PREFIX. "question
        WHERE idQuizz = :idQuizz";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idQuizz', $idQuizz, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $this->hydrateAll($stmt->fetchAll());
    }
}