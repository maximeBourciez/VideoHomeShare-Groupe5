<?php

/**
 * Classe Data Access Object (DAO) pour gérer les thèmes dans la base de données.
 * 
 * Cette classe fournit des méthodes pour interagir avec la table des thèmes,
 * notamment pour effectuer des recherches et créer des objets Theme.
 */
class ThemeDAO {
    /**
     * @var PDO|null $pdo Instance PDO pour l'accès à la base de données.
     */
    private ?PDO $pdo;

    /**
     * Constructeur de la classe ThemeDAO.
     * 
     * @param PDO|null $pdo Instance PDO (optionnelle) pour l'accès à la base de données.
     */
    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * Obtient l'instance PDO associée à cette DAO.
     * 
     * @return PDO|null Instance PDO actuelle.
     */
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    /**
     * Définit l'instance PDO associée à cette DAO.
     * 
     * @param PDO|null $pdo Nouvelle instance PDO.
     * @return void
     */
    public function setPdo(?PDO $pdo): void {
        $this->pdo = $pdo;
    }

    /**
     * Recherche un thème par son identifiant.
     * 
     * @param int|null $id Identifiant du thème à rechercher.
     * @return Theme|null Thème correspondant ou null si introuvable.
     */
    public function find(?int $id): ?Theme{
        $sql="SELECT * FROM ".DB_PREFIX. "theme WHERE id= :id";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute(array("id"=>$id));
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        $theme = $pdoStatement->fetch();

        return $theme;
    }    

    /**
     * Recherche tous les thèmes.
     * 
     * @return Theme[] Liste des thèmes.
     */
    public function findAll(){
        $sql="SELECT * FROM ".DB_PREFIX. "theme";
        $pdoStatement = $this->pdo->prepare($sql);
        $pdoStatement->execute();
        $pdoStatement->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'Theme');
        $theme = $pdoStatement->fetchAll();

        return $theme;
    }


    /**
     * Hydrate un tableau associatif en un objet Theme.
     * 
     * @param array $data Tableau associatif contenant les données d'un thème.
     * 
     * @return Theme Instance de Theme créée à partir des données.
     */
    public function hydrate(array $data): Theme {
        return new Theme(
            $data['id'] ?? null,
            $data['nom'] ?? null
        );
    }


    /**
     * Méthode de récupération des thèmes pour un contenu par son id
     * 
     * @param int $contenuID Identifiant du contenu concerné
     * 
     * @return array Liste des thèmes du contenu
     */
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

    /**
     * Hydrate une liste de tableaux associatifs en une liste d'objets Theme.
     * 
     * @param array $dataList Liste de tableaux associatifs contenant les données des thèmes.
     * @return Theme[] Liste des instances de Theme créées.
     */
    public function hydrateAll(array $dataList): array {
        $themes = [];
        foreach ($dataList as $data) {
            $themes[] = $this->hydrate($data);
        }
        return $themes;
    }
    
  /**
  * Crée un Thème en BD si il n'existe pas
  *
  * @param Theme $theme Theme à vérifier
  *
  * @return Theme|null
  */
    public function createIfNotExists(Theme $theme): ?Theme {
        // Vérifie si le thème existe déjà
        $sql = "SELECT * FROM " . DB_PREFIX . "theme WHERE nom = :nom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $theme->getNom()]);
        $existingTheme = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingTheme) {
            return new Theme($existingTheme['idTheme'], $existingTheme['nom']);
        }

        // Si le thème n'existe pas, on le crée
        $sql = "INSERT INTO " . DB_PREFIX . "theme (nom) VALUES (:nom)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['nom' => $theme->getNom()]);
        
        return new Theme($this->pdo->lastInsertId(), $theme->getNom());
    }

    /**
    * Fonction pour associer un thème à un contenu
    *
    * @param int $themeId Identifiant du thème
    * @param int $contenuId Identifiant du contenu
    *
    * @return bool Indicateur de fonctionnement de la méthode
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

    /**Fonction pour associer un thème à une collection
     * 
     * @param int $themeId Identifiant du thème
     * @param int $collectionId Identifiant de la collection
     * 
     * @return bool Indicateur de fonctionnement de la méthode
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
     * Méthode de récupération des thèmes pour une collection par son id
     * 
     * @param int $collectionId Identifiant de la collection concernée
     * 
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
        $themes = $pdoStatement->fetchAll();

        return $themes;
    }
}

