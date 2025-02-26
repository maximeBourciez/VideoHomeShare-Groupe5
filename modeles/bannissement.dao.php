<?php

/**
 * @file bannissement.dao.php
 * 
 * @brief Data Access Object de la classe Bannissement
 * 
 * @version 1.0.0
 * 
 * @date 14/02/2025
 * 
 * @author Sylvain Trouilh <strouilh@iutbayonne.univ-pau.fr>
 */


class BannissementDAO
{

    /**
     * @var PDO|null $pdo Connexion à la base de données
     */
    private $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe BannissementDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getter
    /**
     * @brief Getter de la connexion à la base de données
     * @return PDO|null Connexion à la base de données
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    // Setter
    /**
     * @brief Setter de la connexion à la base de données
     * @param PDO $pdo Connexion à la base de données
     * @return self
     */
    public function setPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }

    // Méthodes 
    /**
     * @brief d'hydratation de la classe Bannissement
     * @param array $row
     * @return Bannissement
     */
    public function hydrate(array $row): Bannissement
    {   
        $bannissement = new Bannissement();
        $bannissement->setId($row['id']);
        $bannissement->setRaison($row['raison']);
        $bannissement->setDateB(new DateTime($row['dateB']));
        $bannissement->setDateF(new DateTime($row['dateF']));
        $bannissement->setIdUtilisateur($row['idUtilisateur']);
        return $bannissement;
    }

    /**
     * @brief d'hydratation d'un tableau de la classe Bannissement
     * @param array $rows
     * @return Bannissement[]
     */
    public function hydrateAll(array $rows): array
    {
        $bannissements = [];
        foreach($rows as $row){
            $bannissements[] = $this->hydrate($row);
        }
        return $bannissements;
    }


    /**
     * @brief Méthode de création de bannissement
     * 
     * @param Bannissement $bannissement Bannissement à créer
     * @return bool
     */
    public function create(string $raison, string $idUtilisateur, DateTime $dateF): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO ".DB_PREFIX."bannissement (raison, dateB , idUtilisateur,dateF ) VALUES (:raison, now() , :idUtilisateur, :dateF)");
        $stmt->bindParam(":raison", $raison);
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $formattedDateF = $dateF->format('Y-m-d');
        $stmt->bindParam(":dateF", $formattedDateF);
        return $stmt->execute();
    }
    /**
     * @brief  update un bannissement
     * 
     */
    public function update(Bannissement $bannissement){
        $stmt = $this->pdo->prepare("UPDATE ".DB_PREFIX."bannissement SET raison = :raison, dateB = :dateB, dateF = :dateF, idUtilisateur = :idUtilisateur WHERE id = :id");
        $raison = $bannissement->getRaison();
        $dateB = $bannissement->getDateB()->format('Y-m-d');
        $dateF = $bannissement->getDateF()->format('Y-m-d');
        $idUtilisateur = $bannissement->getIdUtilisateur();
        $id = $bannissement->getId();
       
       
        $stmt->bindParam(":raison", $raison);
        $stmt->bindParam(":dateB", $dateB);
        $stmt->bindParam(":dateF", $dateF);
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    /**
     * @brief trouver un utilisateur banni
     * 
     */
    public function find(string $idUtilisateur){

        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."bannissement WHERE idUtilisateur = :idUtilisateur AND dateF > date(now())");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $stmt->execute();
        $fetch = $stmt->fetch();
        if($fetch == false){
        $valeurRetour = $stmt->fetch();
        }else{
            $valeurRetour = $this->hydrate($fetch);
        }
        return $valeurRetour;
    }

    /**
     * @brief trouver les bannisement d'un utilisateur
     */
    public function toutlesBanUsuer(string $idUtilisateur){
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."bannissement WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll());
    }
    /**
     * @brief trouver les bannisement d'un utilisateur
     */
    public function toutlesbannis(){
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."bannissement  WHERE  dateF > NOW()");
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll());
    }
    /**
     * @brief trouver un bannissement
     */
    public function findbyid(int $id){
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."bannissement WHERE id = :id and dateF > date(NOW())");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $this->hydrate($stmt->fetch());
    }


 }

