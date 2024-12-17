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
        $req = $this->pdo->prepare("INSERT INTO Personnalite (nom, prenom, urlImage) VALUES (:nom, :prenom, :urlImage)");
        $req->bindParam(":nom", $personnalite->getNom());
        $req->bindParam(":prenom", $personnalite->getPrenom());
        $req->bindParam(":urlImage", $personnalite->getUrlImage());
        return $req->execute();
    }

    function update(Personnalite $personnalite): bool
    {
        $req = $this->pdo->prepare("UPDATE Personnalite SET nom = :nom, prenom = :prenom, urlImage = :urlImage WHERE id = :id");
        $req->bindParam(":id", $personnalite->getId());
        $req->bindParam(":nom", $personnalite->getNom());
        $req->bindParam(":prenom", $personnalite->getPrenom());
        $req->bindParam(":urlImage", $personnalite->getUrlImage());
        return $req->execute();
    }

    function delete(int $id): bool
    {
        $req = $this->pdo->prepare("DELETE FROM Personnalite WHERE id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }

    function hydrate(array $row): Personnalite
    {
        // Récupération des valeurs
        $id = $row['idPersonaliter'];
        $nom = $row['nom'];
        $prenom = $row['prenom'];
        $urlImage = $row['urlImage'];
        $role = $row['role'];

        // Retourner la Personnalite
        return new Personnalite($id, $nom, $prenom, $urlImage, $role);
    }

    function hydrateAll(array $rows): array
    {
        $personnalites = [];
        foreach ($rows as $row) {
            $personnalite = $this->hydrate($row);
            array_push($personnalites, $personnalite);  // Ajout de la Personnalite au tableau 
        }
        return $personnalites;
    }

    function find(int $id): ?Personnalite
    {
        $sql = "SELECT * FROM Personnalite WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $personnalite = $stmt->fetch();
        if ($personnalite == false) {
            return null;
        }
        return $this->hydrate($personnalite);
    }

    function findAll(): array
    {
        $sql = "SELECT * FROM Personnalite";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    function findAllParContenuId(int $id): array
    {
        $sql = "SELECT * 
                FROM vhs_personnalite AS pe 
                JOIN vhs_participer AS pa ON pa.idPersonaliter = pe.idPersonaliter WHERE pa.idContenu = :id";
        $stmt = $this->pdo->prepare($sql); // Préparation de la requête
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // Liaison des paramètres
        $stmt->execute(); // Exécution de la requête
        $stmt->setFetchMode(PDO::FETCH_ASSOC); // Mode de récupération
        $results = $stmt->fetchAll();
        return $this->hydrateAll($results); // Transformation en entités (si nécessaire)
    }
}
