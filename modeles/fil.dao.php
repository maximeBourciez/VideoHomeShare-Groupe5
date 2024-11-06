<?php

// Classe FilDAO
class FilDAO{
    // Attributs
    private ?PDO $pdo;

    // Constructeur
    public function __construct(?PDO $pdo){
        $this->pdo = $pdo;
    }


    // Encapsulation
    // Getters
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    // Setters
    public function setPdo(PDO $pdo): self{
        $this->pdo = $pdo;
        return $this;
    }


    // Méthodes
    // Méthodes d'hydratation
    public function hydrate(array $row): Fil{
        // Récupération des valeurs
        $id = $row['id'];
        $titre = $row['titre'];
        $dateCreation = new DateTime($row['dateCreation']);
        $description = $row['description'];

        // Retourner le fil
        return new Fil($id, $titre, $dateCreation, $description);
    }

    function hydrateAll(array $rows): array{
        $fils = [];
        foreach($rows as $row){
            $fil = $this->hydrate($row);
            array_push($fils, $fil);  // Ajout du fil au tableau 
        }
        return $fils;
    }


    // Méthodes de recherche 
    // Lister tous les fils
    public function findAll(): array{
        $sql = "SELECT * FROM" . DB_PREFIX . "fil";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    // Trouver un fil par son id
    public function findById(int $id): ?Fil{
        $sql = "SELECT * FROM" . DB_PREFIX . "fil WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrate($stmt->fetch());
    }
}