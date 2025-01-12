<?php

/**
 * @brief Classe d'accès aux données pour les thèmes
 * 
 * Cette classe gère toutes les opérations de lecture et d'écriture
 * des thèmes dans la base de données. Elle permet de gérer les thèmes
 * pour les contenus et les collections.
 * 
 * @author Votre Nom
 * @version 1.0
 * @package Modeles
 */
class ThemeDAO {
    /** @var PDO|null Instance de connexion à la base de données */
    private ?PDO $pdo;

    /**
     * @brief Constructeur de la classe ThemeDAO
     * 
     * @param PDO|null $pdo Instance PDO pour l'accès à la base de données
     */
    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'instance PDO
     * 
     * @return PDO|null Instance PDO actuelle
     */
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    /**
     * @brief Définit l'instance PDO
     * 
     * @param PDO|null $pdo Nouvelle instance PDO
     */
    public function setPdo(?PDO $pdo): void {
        $this->pdo = $pdo;
    }

    /**
     * @brief Recherche un thème par son identifiant
     * 
     * @param int|null $id Identifiant du thème
     * @return Theme|null Le thème trouvé ou null si non trouvé
     */
    public function find(?int $id): ?Theme {
        $sql = "SELECT * FROM " . DB_PREFIX . "theme WHERE id = :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(["id" => $id]);
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        return $pdoStatement->fetch();
    }    

    /**
     * @brief Récupère tous les thèmes
     * 
     * @return Theme[] Liste de tous les thèmes
     */
    public function findAll(): array {
        $sql = "SELECT * FROM " . DB_PREFIX . "theme";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        return $pdoStatement->fetchAll();
    }

    /**
     * @brief Hydrate un thème à partir d'un tableau de données
     * 
     * @param array $data Données du thème
     * @return Theme Instance de Theme créée
     */
    public function hydrate(array $data): Theme {
        return new Theme(
            $data['id'] ?? null,
            $data['nom'] ?? null
        );
    }

    /**
     * @brief Récupère les thèmes associés à un contenu
     * 
     * @param int $contenuId Identifiant du contenu
     * @return array Liste des thèmes associés
     */
    public function findThemesByContenuId(int $contenuId): array {
        $sql = "SELECT t.* 
                FROM " . DB_PREFIX . "theme t
                INNER JOIN " . DB_PREFIX . "caracteriserContenu cc ON t.idTheme = cc.idTheme
                WHERE cc.idContenu = :contenuId";
    
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['contenuId' => $contenuId]);
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        return $pdoStatement->fetchAll();
    }

    /**
     * @brief Hydrate plusieurs thèmes à partir d'un tableau de données
     * 
     * @param array $dataList Liste des données des thèmes
     * @return Theme[] Liste des thèmes créés
     */
    public function hydrateAll(array $dataList): array {
        $themes = [];
        foreach ($dataList as $data) {
            $themes[] = $this->hydrate($data);
        }
        return $themes;
    }
    
    /**
     * @brief Crée un thème s'il n'existe pas déjà
     * 
     * @param Theme $theme Thème à créer
     * @return Theme|null Le thème créé ou existant
     */
    public function createIfNotExists(Theme $theme): ?Theme {
        $sql = "SELECT * FROM " . DB_PREFIX . "theme WHERE nom = :nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $theme->getNom()]);
        $existingTheme = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingTheme) {
            return new Theme($existingTheme['idTheme'], $existingTheme['nom']);
        }

        $sql = "INSERT INTO " . DB_PREFIX . "theme (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $theme->getNom()]);
        
        return new Theme($this->pdo->lastInsertId(), $theme->getNom());
    }

    /**
     * @brief Associe un thème à un contenu
     * 
     * @param int $themeId Identifiant du thème
     * @param int $contenuId Identifiant du contenu
     * @return bool Succès de l'opération
     */
    public function associateThemeWithContenu(int $themeId, int $contenuId): bool {
        $sql = "INSERT INTO " . DB_PREFIX . "caracteriserContenu (idTheme, idContenu) 
                VALUES (:themeId, :contenuId)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'themeId' => $themeId,
            'contenuId' => $contenuId
        ]);
    }

    /**
     * @brief Associe un thème à une collection
     * 
     * @param int $themeId Identifiant du thème
     * @param int $collectionId Identifiant de la collection
     * @return bool Succès de l'opération
     */
    public function associateThemeWithCollection(int $themeId, int $collectionId): bool {
        $sql = "INSERT INTO " . DB_PREFIX . "caracteriserCollection (idTheme, idCollection) 
                VALUES (:themeId, :collectionId)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'themeId' => $themeId,
            'collectionId' => $collectionId
        ]);
    }

    /**
     * @brief Récupère les thèmes d'une collection
     * 
     * @param int $collectionId Identifiant de la collection
     * @return array Liste des thèmes de la collection
     */
    public function findThemesByCollectionId(int $collectionId): array {
        $sql = "SELECT t.* 
                FROM " . DB_PREFIX . "theme t
                INNER JOIN " . DB_PREFIX . "caracteriserCollection cc ON t.idTheme = cc.idTheme
                WHERE cc.idCollection = :collectionId";

        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(['collectionId' => $collectionId]);
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        return $pdoStatement->fetchAll();
    }
}

