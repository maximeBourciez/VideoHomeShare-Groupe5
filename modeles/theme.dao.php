<?php

class ThemeDAO{
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
    public function find(?int $id): ?Theme{
        $sql="SELECT * FROM ".DB_PREFIX. "theme WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        $theme = $pdoStatement->fetch();

        return $theme;
    }
    //But : Trouve une theme en fonction de son identifiant

    public function findAssoc(?int $id): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX."theme WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $theme = $pdoStatement->fetch();
        return $theme;
    }
    //But : Trouve une theme en fonction de son identifiant - Version Assoc

    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "theme";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        $theme = $pdoStatement->fetchAll();

        return $theme;
    }
    //But : Trouve toutes les themes

    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."theme";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $theme = $pdoStatement->fetchAll();
        return $theme;
    }
    //But : Trouve toutes les themes - Version Assoc

    public function findThemesByContenuId(int $contenuId): array {
        $sql = "SELECT t.* 
                FROM " . DB_PREFIX . "theme t
                INNER JOIN " . DB_PREFIX . "caracteriserContenu cc ON t.idTheme = cc.idTheme
                WHERE cc.idContenu = :contenuId";
    
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['contenuId' => $contenuId]);
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        $themes = $pdoStatement->fetchAll();
    
        return $themes;
    }
    //But : Trouve les themes en fonction de l'identifiant du contenu

    //Methodes hydrate
    public function hydrate($tableauAssoc): ?Theme
    {
        $theme = new Theme($tableauAssoc['id'], $tableauAssoc['nom']);

        return $theme;
    }
    //But : Créer une theme avec les valeurs assignées aux attributs correspondants

    public function hydrateAll($tableau): ?array{
        $themes = [];
        foreach($tableau as $tableauAssoc){
            $theme = $this->hydrate($tableauAssoc);
            $themes[] = $theme;
        }

        return $themes;
    }
    //But : Créer les themes avec les valeurs assignées aux attributs correspondants

    
}

?>