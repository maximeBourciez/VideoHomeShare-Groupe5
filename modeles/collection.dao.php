<?php

class CollectionDAO{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    //Encapsulation
    //Getter
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    //Setter
    public function setPdo($pdo): void{
        $this->pdo = $pdo;
    }

    //Methodes
    //A tester quand la BD sera mise en place
    //Methodes find
    public function find(?int $id): ?Collection{
        $sql="SELECT * FROM ".DB_PREFIX. "collection WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Collection');
        $collection = $pdoStatement->fetch();

        return $collection;
    }
    //But : Trouve une collection en fonction de son identifiant

    public function findAssoc(?int $id): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX."collection WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $collection = $pdoStatement->fetch();
        return $collection;
    }
    //But : Trouve une collection en fonction de son identifiant - Version Assoc

    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "collection";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Collection');
        $collection = $pdoStatement->fetchAll();

        return $collection;
    }
    //But : Trouve toutes les collections

    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."collection";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $collection = $pdoStatement->fetchAll();
        return $collection;
    }
    //But : Trouve toutes les collections - Version Assoc

    //Methodes hydrate
    public function hydrate($tableauAssoc): ?Collection
    {
        $collection = new Collection($tableauAssoc['id'], 
                                   $tableauAssoc['type'], 
                                   $tableauAssoc['nom']);

        return $collection;
    }
    //But : Créer une collection avec les valeurs assignées aux attributs correspondants

    public function hydrateAll($tableau): ?array{
        $collections = [];
        foreach($tableau as $tableauAssoc){
            $collection = $this->hydrate($tableauAssoc);
            $collections[] = $collection;
        }

        return $collections;
    }
    //But : Créer les collections avec les valeurs assignées aux attributs correspondants
}

?>