<?php
/**
 * @file jouer.dao.php
 * 
 * @brief Classe JouerDAO
 * 
 * @details Classe permettant de gérer les accès à la base de données pour les attributs de jouer
 * 
 * @date 12/1/2025
 * 
 * @version 1.0
 * 
 * @author Marylou Lohier
 */
class JouerDAO{
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
     * @brief Méthode de création de valeurs jouer
     * 
     * @param Jouer
     * @return bool
     */
    public function create(Jouer $jouer): bool{
        $req = $this->pdo->prepare("INSERT INTO jouer (idUtilisateur, idQuizz, score) VALUES (:idUtilisateur, :idQuizz, :score)");
        $req->bindParam(":idUtilisateur", $jouer->getIdUtilisateur());
        $req->bindParam(":idQuizz", $jouer->getIdQuizz());
        $req->bindParam(":score", $jouer->getScore());

        return $req->execute();
    }

    /**
     * @brief Méthode de mise à jour de résultats
     * 
     * @param Jouer
     * @return bool
     */
    public function update(Jouer $jouer): bool{
        $req = $this->pdo->prepare("UPDATE Quizz SET idUtilisateur = :idUtilisateur, idQuizz = :idQuizz, score = :score WHERE idUtilisateur = :idUtilisateur, idQuizz = :idQuizz");
        $req->bindParam(":idUtilisateur", $jouer->getIdUtilisateur());
        $req->bindParam(":idQuizz", $jouer->getIdQuizz());
        $req->bindParam(":score", $jouer->getScore());

        return $req->execute();
    }

    /**
     * @brief Méthode de suppression d'un résultat
     * 
     * @param idQuizz //identifiant du quizz
     * @param idUtilisateur //identifiant de l'utilisateur
     * @return bool
     */
    public function delete(int $idQuizz, int $idUtilisateur): bool{
        $req = $this->pdo->prepare("DELETE FROM Quizz WHERE idQuizz = :idQuizz AND idUtilisateur = :idUtilisateur");
        $req->bindParam(":idQuizz", $idQuizz);
        $req->bindParam(":idUtilisateur", $idUtilisateur);

        return $req->execute();
    }

    /**
     * @brief Méthode d'hydratation de résultats
     * 
     * @param array //tableau des attributs de la table jouer
     * @return Jouer
     */
    public function hydrate(array $row): Jouer{
        // Récupération des valeurs
        $idQuizz = $row['idQuizz'];
        $idUtilisateur = $row['idUtilisateur'];
        $score = $row['score'];

        // Retourner les valeurs
        return new Jouer($idQuizz, $idUtilisateur, $score);
    }

    /**
     * @brief Méthode d'hydratation de tous les résultats et autres attributs
     * 
     * @param array //tableau des attributs des résultats et autres attributs
     * @return array
     */
    public function hydrateAll(array $rows): array{
        $jouers = [];
        foreach($rows as $row){
            $jouer = $this->hydrate($row);
            array_push($jouers, $jouer);  // Ajout du résultat et autres attributs au tableau 
        }
        return $jouers;
    }

    /**
     * @brief Méthode pour récupérer le résultat et autres attributs d'un quizz
     * 
     * @param $idQuizz identifiant du quizz
     * @param $idUtilisateur identifiant de l'utilisateur
     * @return Jouer
     */
    public function findByQuizzUser(int $idQuizz, int $idUtilisateur): Jouer{
        $sql = "SELECT * FROM " .DB_PREFIX. "jouer WHERE idQuizz = :idQuizz AND idUtilisateur = :idUtilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idQuizz', $idQuizz, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == null){
            return null;
        }

        return $this->hydrate($row);
    }

        /**
     * @brief Renvoie s'il y a un score déjà existant pour l'utilisateur dans le quizz (0 pour non, 1 pour oui)
     * 
     * @param $idQuizz identifiant du quizz
     * @param $idUtilisateur identifiant de l'utilisateur
     * @return bool
     */
    public function verifScoreUser(int $idQuizz, int $idUtilisateur): bool{
        $sql = "SELECT * FROM " .DB_PREFIX. "jouer WHERE idQuizz = :idQuizz AND idUtilisateur = :idUtilisateur";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idQuizz', $idQuizz, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row != null){
            return true;
        }
        return false;
    }

    /**
     * @brief Méthode pour récupérer tous les résultats d'un quizz
     * @param $idQuizz identifiant du quizz
     * @return array
     */
    public function findAllByQuizz($idQuizz): array{
        $sql = "SELECT idUtilisateur, score FROM " .DB_PREFIX. "jouer WHERE idQuizz = :idQuizz";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $this->hydrateAll($stmt->fetchAll());
    }
}