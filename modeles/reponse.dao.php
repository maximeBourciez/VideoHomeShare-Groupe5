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
        $reponse = new Reponse($tableauAssoc['idReponse'], 
                                $tableauAssoc['valeur'], 
                                $tableauAssoc['rang'], 
                                $tableauAssoc['estVraie'],
                                $tableauAssoc['idQuestion']);

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

    public function findByQuestionId(int $idQuestion): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX. "reponse WHERE idQuestion = :idQuestion";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idQuestion', $idQuestion, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Méthode permettant de créer une réponse en BD
     * 
     * @param Reponse $reponse
     * 
     * @return bool
     */
    public function create(Reponse $reponse): bool{
        $sql = "INSERT INTO " . DB_PREFIX . "reponse (valeur, rang, estVraie, idQuestion) VALUES (:valeur, :rang, :estVraie, :idQuestion)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':valeur', $reponse->getValeur(), PDO::PARAM_STR);
        $stmt->bindValue(':rang', $reponse->getRang(), PDO::PARAM_INT);
        $stmt->bindValue(':estVraie', $reponse->getVerite(), PDO::PARAM_BOOL);
        $stmt->bindValue(':idQuestion', $reponse->getIdQuestion(), PDO::PARAM_INT);
        return $stmt->execute();
    }
}

