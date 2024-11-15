<?php

class SignalementDAO{
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
    public function find(?int $id): ?Signalement{
        $sql="SELECT * FROM ".DB_PREFIX. "signalement WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Signalement');
        $signalement = $pdoStatement->fetch();

        return $signalement;
    }
    //But : Trouve une signalement en fonction de son identifiant

    public function findAssoc(?int $id): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX."signalement WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $signalement = $pdoStatement->fetch();
        return $signalement;
    }
    //But : Trouve une signalement en fonction de son identifiant - Version Assoc

    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "signalement";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Signalement');
        $signalement = $pdoStatement->fetchAll();

        return $signalement;
    }
    //But : Trouve toutes les signalements

    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."signalement";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }
    //But : Trouve toutes les signalements - Version Assoc

    //Methodes hydrate
    public function hydrate($tableauAssoc): ?Signalement
    {
        $signalement = new Signalement($tableauAssoc['id'], $tableauAssoc['raison']);

        return $signalement;
    }
    //But : Créer une signalement avec les valeurs assignées aux attributs correspondants

    public function hydrateAll($tableau): ?array{
        $signalements = [];
        foreach($tableau as $tableauAssoc){
            $signalement = $this->hydrate($tableauAssoc);
            $signalements[] = $signalement;
        }

        return $signalements;
    }
    //But : Créer les signalements avec les valeurs assignées aux attributs correspondants
}

?>