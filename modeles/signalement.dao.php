<?php


/**
 * @file signalement.dao.php
 * 
 * @brief Signalement Data Access Object
 * 
 * @details Data Access Object de la classe Signalement
 * 
 * @version 1.0
 * 
 * @date 18/12/2024
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */
class SignalementDAO{
    // Attributs
    /**
     * @var PDO|null $pdo Connexion à la base de données
     */
    private ?PDO $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe SignalementDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    //Encapsulation
    //Getter
    /**
     * @brief Getter de l'attribut pdo
     * 
     * @return PDO|null
     */
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    //Setter
    /**
     * @brief Setter de l'attribut pdo
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    public function setPdo($pdo): void{
        $this->pdo = $pdo;
    }

    //Methodes
    /**
     * @brief Trouve une signalement en fonction de son identifiant
     * 
     * @param int|null $id Identifiant du signalement
     * 
     * @return Signalement|null
     */
    public function find(?int $id): ?Signalement{
        $sql="SELECT * FROM ".DB_PREFIX. "signalement WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Signalement');
        $signalement = $pdoStatement->fetch();

        return $signalement;
    }
    
    /**
     * @brief Trouve une signalement en fonction de son identifiant - Version Assoc
     * 
     * @param int|null $id Identifiant du signalement
     * 
     * @return array|null
     */
    public function findAssoc(?int $id): ?array
    {
        $sql="SELECT * FROM ".DB_PREFIX."signalement WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $signalement = $pdoStatement->fetch();
        return $signalement;
    }
    
    /**
     * @brief Trouve toutes les signalements
     * 
     * @return array|null
     * 
     * @details Trouve toutes les signalements
     */
    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "signalement";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Signalement');
        $signalement = $pdoStatement->fetchAll();

        return $signalement;
    }
    
    /**
     * @brief Trouve toutes les signalements - Version Assoc
     * 
     * @return array|null
     * 
     * @details Trouve toutes les signalements
     */
    public function findAllAssoc(){
        $sql="SELECT * FROM ".DB_PREFIX."signalement";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $signalement = $pdoStatement->fetchAll();
        return $signalement;
    }
    

    //Methodes hydrate
    /**
     * @brief Hydrate un signalement
     * 
     * @param array $tableauAssoc Tableau associatif contenant les informations du signalement
     * 
     * @return Signalement|null
     */
    public function hydrate($tableauAssoc): ?Signalement
    {
        $signalement = new Signalement($tableauAssoc['id'], $tableauAssoc['raison']);

        return $signalement;
    }
    
    /**
     * @brief Hydrate tous les signalements
     * 
     * @param array $tableau Tableau contenant les informations des signalements
     * 
     * @return array|null
     */
    public function hydrateAll($tableau): ?array{
        $signalements = [];
        foreach($tableau as $tableauAssoc){
            $signalement = $this->hydrate($tableauAssoc);
            $signalements[] = $signalement;
        }

        return $signalements;
    }

    //Methodes
    /**
     * @brief Ajoute un signalement
     * 
     * @param Signalement $signalement Signalement à ajouter
     * 
     * @return int
     */
    public function ajouterSignalement(Signalement $signalement): int{
        var_dump($signalement);
        // Préparer la requête
        $sql = "INSERT INTO ".DB_PREFIX."signalement (raison, idMessage, idUtilisateur) VALUES (:raison, :idMessage, :idUtilisateur)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':raison', $signalement->getRaison()->toString(), PDO::PARAM_STR);
        $stmt->bindValue(':idMessage', $signalement->getIdMessage(), PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $signalement->getIdUtilisateur(), PDO::PARAM_STR);

        // Exécuter la requête et récupérer l'identifiant
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    public function findAllMessageSignaleAssoc() : ?array{
        $sql = "SELECT s.idMessage , m.valeur  COUNT(s.idMessage) AS nbSignalement FROM ".DB_PREFIX."signalement  s join ".DB_PREFIX."message m on s.idMessage = m.idMessage GROUP BY s.idMessage; ";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_ASSOC);
        $signalements = $pdoStatement->fetchAll();
        return $signalements;
    }
}

?>