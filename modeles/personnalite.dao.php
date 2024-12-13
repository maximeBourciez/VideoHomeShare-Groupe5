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
    function create(Personnalite $personnalite): bool
    {
        $req = $this->pdo->prepare("INSERT INTO " . DB_PREFIX . "personnalite (nom, prenom, urlImage) VALUES (:nom, :prenom, :urlImage)");
        $nom = $personnalite->getNom();
        $prenom = $personnalite->getPrenom();
        $urlImage = $personnalite->getUrlImage();
        
        $req->bindParam(":nom", $nom);
        $req->bindParam(":prenom", $prenom);
        $req->bindParam(":urlImage", $urlImage);
        
        $success = $req->execute();
        
        if ($success) {
            // Récupérer l'ID généré et le mettre dans l'objet
            $id = $this->pdo->lastInsertId();
            $personnalite->setId((int)$id);
        }
        
        return $success;
    }

    function update(Personnalite $personnalite): bool
    {
        $req = $this->pdo->prepare("UPDATE " . DB_PREFIX . "personnalite SET nom = :nom, prenom = :prenom, urlImage = :urlImage WHERE id = :id");
        $id = $personnalite->getId();
        $nom = $personnalite->getNom();
        $prenom = $personnalite->getPrenom();
        $urlImage = $personnalite->getUrlImage();
        
        $req->bindParam(":id", $id);
        $req->bindParam(":nom", $nom);
        $req->bindParam(":prenom", $prenom);
        $req->bindParam(":urlImage", $urlImage);
        return $req->execute();
    }

    function delete(int $id): bool
    {
        $req = $this->pdo->prepare("DELETE FROM " . DB_PREFIX . "personnalite WHERE id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }

    public function hydrate($row): ?Personnalite {
        if (!$row) {
            return null;
        }

        // Récupération directe des valeurs depuis le tableau associatif
        $id = $row['id'] ?? $row['idPersonaliter'] ?? null;
        $nom = $row['nom'];
        $prenom = $row['prenom'];
        $urlImage = $row['urlImage'];
        $role = $row['role'] ?? null;

        // Retourner la Personnalite
        return new Personnalite($id, $nom, $prenom, $urlImage, $role);
    }

    function hydrateAll(array $rows): array
    {
        $personnalites = [];
        foreach ($rows as $row) {
            $personnalite = $this->hydrate($row);
            if ($personnalite) {
                $personnalites[] = $personnalite;
            }
        }
        return $personnalites;
    }

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
