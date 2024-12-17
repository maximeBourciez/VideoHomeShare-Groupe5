<?php

class PersonnaliteDAO
{
    // Attributs
    private $pdo;

    // Constructeur
    function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    // Méthodes
    function find(int $id): ?Personnalite
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "personnalite WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $personnalite = $stmt->fetch();
        return $this->hydrate($personnalite);
    }

    function findAll(): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "personnalite";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    function findAllParContenuId(int $id): array
    {
        $sql = "SELECT * 
                FROM " . DB_PREFIX . "personnalite AS pe 
                JOIN " . DB_PREFIX . "participer AS pa ON pa.idPersonaliter = pe.idPersonaliter 
                WHERE pa.idContenu = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }
}
