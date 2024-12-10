<?php

/**
 * Classe représentant un Data Access Object (DAO) pour la collection.
 *
 * Cette classe permet d'interagir avec la base de données pour effectuer des opérations
 * telles que récupérer, compter ou manipuler les collections et leurs saisons/épisodes.
 */
class CollectionDAO {
    
    /**
     * @var PDO|null $pdo Instance de la connexion à la base de données.
     */
    private ?PDO $pdo;

    /**
     * Constructeur de la classe CollectionDAO.
     *
     * @param PDO|null $pdo Instance de la connexion PDO (par défaut null).
     */
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    /**
     * Récupère l'instance PDO de la classe.
     *
     * @return PDO|null L'instance de la connexion PDO.
     */
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    /**
     * Définit l'instance PDO de la classe.
     *
     * @param PDO $pdo Instance de la connexion PDO.
     */
    public function setPdo($pdo): void{
        $this->pdo = $pdo;
    }

    /**
     * Trouve une collection en fonction de son identifiant.
     *
     * @param int|null $id Identifiant de la collection.
     * @return Collection|null La collection trouvée ou null si aucun résultat.
     */
    public function find(?int $id): ?Collection{
        $sql="SELECT * FROM ".DB_PREFIX. "collection WHERE idCollection = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Collection');
        $collection = $pdoStatement->fetch();

        return $collection;
    }

    /**
     * Compte le nombre de saisons d'une collection.
     *
     * @param int|null $id Identifiant de la collection.
     * @return int|null Le nombre de saisons ou null si aucune donnée.
     */
    public function countSaisons(?int $id): ?int {
        $sql = "SELECT MAX(saison) AS nbSaison FROM " . DB_PREFIX . "inclure WHERE idCollection = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['nbSaison'] : null; // Extraire la valeur et caster en entier
    }

    /**
     * Compte le nombre d'épisodes d'une collection.
     *
     * @param int|null $id Identifiant de la collection.
     * @return int|null Le nombre d'épisodes ou null si aucune donnée.
     */
    public function countEpisodes(?int $id): ?int {
        $sql = "SELECT COUNT(rang) AS nbEpisode FROM " . DB_PREFIX . "inclure WHERE idCollection = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id" => $id));
        $result = $pdoStatement->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['nbEpisode'] : null; // Extraire la valeur et caster en entier
    }

    /**
     * Trouve une collection en fonction de son identifiant, version associative.
     *
     * @param int|null $id Identifiant de la collection.
     * @return array|null Tableau associatif représentant la collection ou null si aucun résultat.
     */
    public function findAssoc(?int $id): ?array {
        $sql="SELECT * FROM ".DB_PREFIX."collection WHERE idCollection = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $collection = $pdoStatement->fetch();
        return $collection;
    }

    /**
     * Trouve toutes les collections.
     *
     * @return Collection[] Tableau de toutes les collections trouvées.
     */
    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "collection";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Collection');
        $collections = $pdoStatement->fetchAll();

        return $collections;
    }

    /**
     * Trouve toutes les collections, version associative.
     *
     * @return array[] Tableau associatif de toutes les collections trouvées.
     */
    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."collection";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $collections = $pdoStatement->fetchAll();
        return $collections;
    }

    /**
     * Hydrate une collection à partir d'un tableau associatif.
     *
     * @param array $tableauAssoc Tableau associatif contenant les données pour hydrater la collection.
     * @return Collection La collection créée avec les valeurs assignées.
     */
    public function hydrate($tableauAssoc): ?Collection {
        $collection = new Collection($tableauAssoc['id'], 
                                     $tableauAssoc['type'], 
                                     $tableauAssoc['nom']);
        return $collection;
    }

    /**
     * Hydrate toutes les collections à partir d'un tableau de tableaux associatifs.
     *
     * @param array $tableau Tableau de tableaux associatifs pour hydrater plusieurs collections.
     * @return Collection[] Tableau de collections créées avec les valeurs assignées.
     */
    public function hydrateAll($tableau): ?array {
        $collections = [];
        foreach($tableau as $tableauAssoc){
            $collection = $this->hydrate($tableauAssoc);
            $collections[] = $collection;
        }
        return $collections;
    }
}

?>
