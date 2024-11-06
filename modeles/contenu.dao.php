<?php

class ContenuDAO{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    //get pdo
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    // set pdo
    public function setPdo(PDO $pdo){
        $this->pdo = $pdo;
    }

    // retourne tout  les contenus de la base de données dans un tableau d'associations
    public function findAll(): array{
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."contenu");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $contenus = [];
        foreach ($result as $row){
            $contenus[] = new Contenu($row['id'], $row['titre'], new DateTime($row['date']), $row['description'], $row['lienAffiche'], $row['durée'], $row['type']);
        }
        return $contenus;
    }
    // retourne un contenu de la base de données par l'id
    public function findById(int $id): ?array{
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."contenu WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $contenu = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($contenu == null){
            return null;
        }
        return ($contenu);
    }

    // transforme un tableau d'associations en un objet de type Contenu
    public function hydrate(array $tableauAssaus): Contenu{
        return new Contenu($tableauAssaus['id'], $tableauAssaus['titre'], new DateTime($tableauAssaus['date']), $tableauAssaus['description'], $tableauAssaus['lienAffiche'], $tableauAssaus['durée'], $tableauAssaus['type']);
    }

    // tansforme un tableau d'associations en un tableau d'objets de type Contenu
    public function hydrateAll(array $tableauAssaus): ?array{
        $contenus = [];
        foreach ($tableauAssaus as $row){
            $contenus[] = new Contenu($row['id'], $row['titre'], new DateTime($row['date']), $row['description'], $row['lienAffiche'], $row['durée'], $row['type']);
        }
        return $contenus;
    }

}