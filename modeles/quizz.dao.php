<?php

class QuizzDAO{

    // Attributs
    private $pdo;

    // Constructeur
    function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    // Méthodes

    function create(Quizz $quizz): bool{
        $req = $this->pdo->prepare("INSERT INTO Quizz (titre, description, difficulte, dateC, idUtilisateur) VALUES (:titre, :description, :difficulte, :date, :idUtilisateur)");
        $req->bindParam(":titre", $quizz->getTitre());
        $req->bindParam(":description", $quizz->getDescription());
        $req->bindParam(":difficulte", $quizz->getDifficulte());
        $req->bindParam(":dateC", $quizz->getDateC());
        $req->bindParam(":idUtilisateur", $quizz->getIdUtilisateur());

        return $req->execute();
    }

    function update(Quizz $quizz): bool{
        $req = $this->pdo->prepare("UPDATE Quizz SET titre = :titre, description = :description, difficulte = :difficulte, dateC = :date WHERE id = :id, idUtilisateur = :idUtilisateur");
        $req->bindParam(":id", $quizz->getId());
        $req->bindParam(":titre", $quizz->getTitre());
        $req->bindParam(":description", $quizz->getDescription());
        $req->bindParam(":difficulte", $quizz->getDifficulte());
        $req->bindParam(":dateC", $quizz->getDateC());
        $req->bindParam(":idUtilisateur", $quizz->getIdUtilisateur());

        return $req->execute();
    }

    function delete(int $id): bool{
        $req = $this->pdo->prepare("DELETE FROM Quizz WHERE id = :id");
        $req->bindParam(":id", $id);

        return $req->execute();
    }

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

    function hydrateAll(array $rows): array{
        $quizzs = [];
        foreach($rows as $row){
            $quizz = $this->hydrate($row);
            array_push($quizzs, $quizz);  // Ajout du Quizz au tableau 
        }
        
        return $quizzs;
    }

    function findById(int $id): ?Quizz{
        $sql = "SELECT Q.*, U.pseudo FROM Quizz Q JOIN Utilisateur U ON Q.idUtilisateur = U.idUtilisateur WHERE idQuizz = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row == null){
            return null;
        }

        return $this->hydrate($row);
    }

    function findAll(): array{
        $sql = "SELECT Q.idQuizz, Q.titre, Q.description, Q.difficulte, Q.dateC, U.pseudo FROM " .DB_PREFIX. "quizz Q JOIN " .DB_PREFIX. "utilisateur U ON Q.idUtilisateur = U.idUtilisateur";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $this->hydrateAll($stmt->fetchAll());
    }
}