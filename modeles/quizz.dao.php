<?php
/**
 * @file quizz.dao.php
 * 
 * @brief Classe QuizzDAO
 * 
 * @details Classe permettant de gérer les accès à la base de données pour les quizz
 * 
 * @date 11/1/2025
 * 
 * @version 2.0
 * 
 * @author Marylou Lohier
 */
class QuizzDAO{
    // Attributs
    /**
     * @var PDO|null $pdo Connexion à la base de données
     */
    private $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe QuizzDAO
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
     * @param quizz
     * @return int
     */
    function create(string $titre, string $description, int $difficulte, string $dateC, string $idUtilisateur): int{
        $req = $this->pdo->prepare("INSERT INTO vhs_quizz (titre, description, difficulte, dateC, idUtilisateur) VALUES (:titre, :description, :difficulte, :dateC, :idUtilisateur)");
        $req->bindParam(":titre", $titre);
        $req->bindParam(":description", $description);
        $req->bindParam(":difficulte", $difficulte);
        $req->bindParam(":dateC", $dateC);
        $req->bindParam(":idUtilisateur", $idUtilisateur);
        $req->execute();

        return $this->pdo->lastInsertId();
    }

    /**
     * @brief Méthode de mise à jour d'un quizz
     * 
     * @param quizz
     * @return bool
     */
    function update(Quizz $quizz): bool{
        $req = $this->pdo->prepare("UPDATE Quizz SET titre = :titre, description = :description, difficulte = :difficulte, dateC = :dateC WHERE id = :id, idUtilisateur = :idUtilisateur");
        $req->bindParam(":idQuizz", $quizz->getId());
        $req->bindParam(":titre", $quizz->getTitre());
        $req->bindParam(":description", $quizz->getDescription());
        $req->bindParam(":difficulte", $quizz->getDifficulte());
        $req->bindParam(":dateC", $quizz->getDate());
        $req->bindParam(":idUtilisateur", $quizz->getIdUtilisateur());

        return $req->execute();
    }

    /**
     * @brief Méthode de suppression d'un quizz
     * 
     * @param idQuizz //identifiant du quizz
     * @return bool
     */
    function delete(int $idQuizz): bool{
        $req = $this->pdo->prepare("DELETE FROM " . DB_PREFIX . "quizz WHERE idQuizz = :idQuizz");
        $req->bindParam(":idQuizz", $idQuizz);

        return $req->execute();
    }

    /**
     * @brief Méthode d'hydratation d'un quizz
     * 
     * @param array //tableau des attributs du quizz
     * @return Quizz
     */
    function hydrate(array $row): Quizz{
        // Récupération des valeurs
        $id = $row['idQuizz'];
        $titre = $row['titre'];
        $description = $row['description'];
        $difficulte = $row['difficulte'];
        $dateC = $row['dateC'];
        $pseudo = $row['pseudo'];

        // Retourner le Quizz
        return new Quizz($id, $titre, $description, $difficulte, $dateC, $pseudo);
    }

    /**
     * @brief Méthode d'hydratation de tous les quizz
     * 
     * @param array //tableau des attributs des quizz
     * @return array
     */
    function hydrateAll(array $rows): array{
        $quizz = [];
        foreach($rows as $row){
            $quiz = $this->hydrate($row);
            array_push($quizz, $quiz);  // Ajout du Quizz au tableau 
        }
        return $quizz;
    }

    /**
     * @brief Méthode pour récupérer les attributs d'un quizz
     * 
     * @param $id //identifiant du quizz
     * @return Quizz
     */
    function find(int $idQuizz): ?Quizz{
        $sql = "SELECT Q.*, U.pseudo FROM " .DB_PREFIX. "quizz Q JOIN " .DB_PREFIX. "utilisateur U ON Q.idUtilisateur = U.idUtilisateur WHERE idQuizz = :idQuizz";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idQuizz', $idQuizz, PDO::PARAM_INT);
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
        $sql = "SELECT Q.*, U.pseudo FROM " .DB_PREFIX. "quizz Q JOIN " .DB_PREFIX. "utilisateur U ON Q.idUtilisateur = U.idUtilisateur";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $this->hydrateAll($stmt->fetchAll());
    }

        /**
     * @brief Méthode pour récupérer les attributs des quizz d'un utilisateur
     * @param $idUtilisateur id de l'utilisateur
     * @return array
     */
    function findAllByUser(string $idUtilisateur): ?array{
        $sql = "SELECT Q.*, U.pseudo FROM " .DB_PREFIX. "quizz Q JOIN " .DB_PREFIX. "utilisateur U ON Q.idUtilisateur = U.idUtilisateur WHERE Q.idUtilisateur = :idUtilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(":idUtilisateur", $idUtilisateur);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $this->hydrateAll($stmt->fetchAll());
    }

    function findByTitreUser(string $titre, string $idUtilisateur): ?Quizz{
        $sql = "SELECT Q.*, U.pseudo FROM " .DB_PREFIX. "quizz Q JOIN " .DB_PREFIX. "utilisateur U ON Q.idUtilisateur = U.idUtilisateur WHERE Q.titre = :titre AND Q.idUtilisateur = :idUtilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titre', $titre);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == null){
            return null;
        }

        return $this->hydrate($row);
    }
}