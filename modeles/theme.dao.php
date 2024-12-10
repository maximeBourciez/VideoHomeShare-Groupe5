
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
     * @return Theme Instance de Theme créée à partir des données.
     */
    public function hydrate(array $data): Theme {
        return new Theme(
            $data['id'] ?? null,
            $data['nom'] ?? null
        );
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
}


?>