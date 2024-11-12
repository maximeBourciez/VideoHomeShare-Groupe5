<?php

// Classe FilDAO
// Classe FilDAO
class FilDAO {
    // Attributs
    private ?PDO $pdo;

    // Constructeur
    public function __construct(?PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getters
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    // Setters
    public function setPdo(PDO $pdo): self {
        $this->pdo = $pdo;
        return $this;
    }

    // Méthodes
    // Méthodes d'hydratation
    public function hydrate(array $row): Fil {
        // Création de l'objet Utilisateur
        $user = new Utilisateur();
        $user->setId($row['idUtilisateur']);
        $user->setPseudo($row['pseudo']);
        $user->setUrlImageProfil($row['urlImageProfil']);  // Correction de setUrlImageProfil

        // Création de l'objet Fil en incluant l'utilisateur
        $id = $row['idFil'];
        $titre = $row['titre'];
        $dateCreation = new DateTime($row['dateC']);
        $description = $row['description'];

        $fil = new Fil($id, $titre, $dateCreation, $description);
        $fil->setUtilisateur($user);  // Associer l'utilisateur au fil (en supposant que Fil a cette méthode)

        return $fil;
    }

    function hydrateAll(array $rows): array {
        $fils = [];
        foreach ($rows as $row) {
            $fils[] = $this->hydrate($row);  // Ajout du fil au tableau
        }
        return $fils;
    }

    // Méthodes de recherche 
    // Lister tous les fils avec le premier utilisateur ayant posté
    public function findAll(): array {
        $sql = "
            SELECT f.*, u.idUtilisateur, u.pseudo, u.urlImageProfil
            FROM " . DB_PREFIX . "fil AS f
            LEFT JOIN (
                SELECT m.idFil, MIN(m.dateC) AS firstDate
                FROM " . DB_PREFIX . "message AS m
                GROUP BY m.idFil
            ) AS first_message ON f.idFil = first_message.idFil
            LEFT JOIN " . DB_PREFIX . "message AS m ON f.idFil = m.idFil AND m.dateC = first_message.firstDate
            LEFT JOIN " . DB_PREFIX . "utilisateur AS u ON m.idUtilisateur = u.idUtilisateur;
        ";
        
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    // Trouver un fil par son id
    public function findById(int $id): ?Fil {
        $sql = "SELECT * FROM " . DB_PREFIX . "fil WHERE idFil = :id";  // Correction du nom de colonne
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrate($stmt->fetch());
    }
}