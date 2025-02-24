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
     * @brief Méthode de création de bannissement
     * 
     * @param Bannissement $bannissement Bannissement à créer
     * @return bool
     */
    public function create(string $raison, string $idUtilisateur): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO bannissement (raison, dateB , idUtilisateur) VALUES (:raison,now(), :idUtilisateur)");
        $stmt->bindParam(":raison", $raison);
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        return $stmt->execute();
    }
}
