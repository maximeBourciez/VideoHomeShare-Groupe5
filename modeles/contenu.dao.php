<?php

class ContenuDAO{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    //get pdo
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    // set pdo
    public function setPdo(PDO $pdo){
        $this->pdo = $pdo;
    }

    // retourne tout  les contenus de la base de données dans un tableau d'associations
    public function findAll(): array{
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."contenu");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $contenus = $this->hydrateAll($result);
        return $contenus;
    }
    // retourne un contenu de la base de données par l'id
    public function findById(int $id): ?Contenu {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX."contenu WHERE idContenu = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $contenu = $this->hydrate($result);
        return $contenu;
    }

    // transforme un tableau d'associations en un objet de type Contenu
    public function hydrate(array $tableauAssaus): Contenu{
        return new Contenu($tableauAssaus['idContenu'], $tableauAssaus['titre'], new DateTime($tableauAssaus['dateS']), $tableauAssaus['description'],$tableauAssaus['DescriptionLongue'], $tableauAssaus['lienAffiche'], $tableauAssaus['duree'], $tableauAssaus['type']);
    }

    // tansforme un tableau d'associations en un tableau d'objets de type Contenu
    public function hydrateAll(array $tableauAssaus): ?array{
        $contenus = [];
        foreach ($tableauAssaus as $row){
            $contenus[] = $this->hydrate($row);
        }
        return $contenus;
    }

    /**
     * Crée un contenu à partir d'un ID TMDB
     */
    public function createFromTmdb(int $tmdbId): ?Contenu {
        // Initialiser l'API TMDB avec votre clé
        $tmdbApi = new TmdbAPI('a2096553592bde8ead1b2a0f2fa59bc0');
        
        // Récupérer les données du film
        $movieData = $tmdbApi->getMovieById($tmdbId);
        if (!$movieData) {
            return null;
        }

        // Convertir en objet Contenu
        $contenu = $tmdbApi->convertToContenu($movieData);
        
        // Insérer le contenu dans la base de données
        $sql = "INSERT INTO " . DB_PREFIX . "contenu (titre, dateS, description, DescriptionLongue, lienAffiche, duree, type) 
                VALUES (:titre, :dateS, :description, :descriptionLongue, :lienAffiche, :duree, :type)";
        
        $stmt = $this->pdo->prepare($sql);
        $date = $contenu->getDate()->format('Y-m-d');
        
        $stmt->execute([
            ':titre' => $contenu->getTitre(),
            ':dateS' => $date,
            ':description' => $contenu->getDescription(),
            ':descriptionLongue' => $contenu->getDescriptionLongue(),
            ':lienAffiche' => $contenu->getLienAffiche(),
            ':duree' => $contenu->getDuree(),
            ':type' => $contenu->getType()
        ]);

        // Récupérer l'ID généré
        $contenu->setId($this->pdo->lastInsertId());

        // Ajouter les personnalités
        $personnalites = $tmdbApi->getPersonnalites($movieData);
        $personnaliteDAO = new PersonnaliteDAO($this->pdo);
        
        foreach ($personnalites as $personnalite) {
            $personnaliteDAO->create($personnalite);
            // Ajouter la relation dans la table participer
            $this->addPersonnaliteToContenu($personnalite->getId(), $contenu->getId(), $personnalite->getRole());
        }

        return $contenu;
    }

    private function addPersonnaliteToContenu(int $idPersonnalite, int $idContenu, string $role): void {
        $sql = "INSERT INTO " . DB_PREFIX . "participer (idPersonaliter, idContenu, role) 
                VALUES (:idPersonnalite, :idContenu, :role)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':idPersonnalite' => $idPersonnalite,
            ':idContenu' => $idContenu,
            ':role' => $role
        ]);
    }

    public function addTheme(int $contenuId, int $themeId): bool {
        $sql = "INSERT INTO " . DB_PREFIX . "avoir_theme (idContenu, idTheme) 
                VALUES (:contenuId, :themeId)";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':contenuId' => $contenuId,
            ':themeId' => $themeId
        ]);
    }

    public function removeTheme(int $contenuId, int $themeId): bool {
        $sql = "DELETE FROM " . DB_PREFIX . "avoir_theme 
                WHERE idContenu = :contenuId AND idTheme = :themeId";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':contenuId' => $contenuId,
            ':themeId' => $themeId
        ]);
    }

}