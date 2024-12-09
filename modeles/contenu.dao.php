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
        $contenus = $this->hydrateAll($result);
        return $contenus;
    }
    // retourne un contenu de la base de données par l'id
    public function findById(int $id): ?Contenu {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."contenu WHERE idContenu = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $contenu = $this->hydrate($result);
        return $contenu;
    }

    // transforme un tableau d'associations en un objet de type Contenu
    public function hydrate(array $tableauAssaus): Contenu{
        return new Contenu($tableauAssaus['idContenu'], $tableauAssaus['titre'], new DateTime($tableauAssaus['dateS']), $tableauAssaus['description'],$tableauAssaus['DescriptionLongue'], $tableauAssaus['lienAffiche'], $tableauAssaus['duree'], $tableauAssaus['type']);
    }

    // tansforme un tableau d'associations en un tableau d'objets de type Contenu
    public function hydrateAll(array $tableauAssaus): ?array{
        $contenus = [];
        foreach ($tableauAssaus as $row){
            $contenus[] = $this->hydrate($row);
        }
        return $contenus;
    }

}