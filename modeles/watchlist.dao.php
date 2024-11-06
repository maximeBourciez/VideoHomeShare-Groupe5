<?php

class WatchlistDAO{
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
    public function find(?int $id): ?Watchlist{
        $sql="SELECT * FROM ".DB_PREFIX. "watchlist WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Watchlist');
        $watchlist = $pdoStatement->fetch();

        return $watchlist;
    }
    //But : Trouve une watchlist en fonction de son identifiant

    public function findAssoc(?int $id): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX."watchlist WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $watchlist = $pdoStatement->fetch();
        return $watchlist;
    }
    //But : Trouve une watchlist en fonction de son identifiant - Version Assoc

    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "watchlist";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Watchlist');
        $watchlist = $pdoStatement->fetchAll();

        return $watchlist;
    }
    //But : Trouve toutes les watchlists

    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."categorie";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $watchlist = $pdoStatement->fetchAll();
        return $watchlist;
    }
    //But : Trouve toutes les watchlists - Version Assoc

    //Methodes hydrate
    public function hydrate($tableauAssoc): ?Watchlist
    {
        $watchlist = new Watchlist($tableauAssoc['id'], 
                                   $tableauAssoc['nom'], 
                                   $tableauAssoc['description'], 
                                   $tableauAssoc['estPublique'], 
                                   $tableauAssoc['date']);

        return $watchlist;
    }
    //But : Créer une watchlist avec les valeurs assignées aux attributs correspondants

    public function hydrateAll($tableau): ?array{
        $watchlists = [];
        foreach($tableau as $tableauAssoc){
            $watchlist = $this->hydrate($tableauAssoc);
            $watchlists[] = $watchlist;
        }

        return $watchlists;
    }
    //But : Créer les watchlists avec les valeurs assignées aux attributs correspondants
}

?>