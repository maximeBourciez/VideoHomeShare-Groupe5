<?php

class ReponseDAO{
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
    public function find(?int $id): ?Reponse{
        $sql="SELECT * FROM ".DB_PREFIX. "reponse WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Reponse');
        $reponse = $pdoStatement->fetch();

        return $reponse;
    }
    //But : Trouve une reponse en fonction de son identifiant

    public function findAssoc(?int $id): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX."reponse WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $reponse = $pdoStatement->fetch();
        return $reponse;
    }
    //But : Trouve une reponse en fonction de son identifiant - Version Assoc

    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "reponse";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Reponse');
        $reponse = $pdoStatement->fetchAll();

        return $reponse;
    }
    //But : Trouve toutes les reponses

    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."reponse";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $reponse = $pdoStatement->fetchAll();
        return $reponse;
    }
    //But : Trouve toutes les reponses - Version Assoc

    //Methodes hydrate
    public function hydrate($tableauAssoc): ?Reponse
    {
        $reponse = new Reponse($tableauAssoc['id'], 
                                   $tableauAssoc['valeur'], 
                                   $tableauAssoc['rang'], 
                                   $tableauAssoc['estVraie']);

        return $reponse;
    }
    //But : Créer une reponse avec les valeurs assignées aux attributs correspondants

    public function hydrateAll($tableau): ?array{
        $reponses = [];
        foreach($tableau as $tableauAssoc){
            $reponse = $this->hydrate($tableauAssoc);
            $reponses[] = $reponse;
        }

        return $reponses;
    }
    //But : Créer les reponses avec les valeurs assignées aux attributs correspondants

    public function findByQuestionId($idQuestion): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX. "reponse WHERE idQuestion = :idQuestion";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("idQuestion"=>$idQuestion));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Reponse');
        $reponses = $pdoStatement->fetchAll();

        return $reponses;
    }
}

?>