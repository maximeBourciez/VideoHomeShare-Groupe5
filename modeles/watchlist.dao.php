<?php  

class WatchlistDAO {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @brief Méthode pour créer une nouvelle watchlist
     * 
     * @param string $nom Nom de la watchlist
     * @param string $description Description de la watchlist
     * @param bool $estPublique Statut public/privé de la watchlist
     * @param string $idUtilisateur Identifiant de l'utilisateur créateur
     * 
     * @return int Identifiant de la watchlist créée
     */
    public function create(string $nom, string $description, bool $estPublique, string $idUtilisateur): int
    {
        $sql = "
            INSERT INTO " . DB_PREFIX . "watchlist (nom, description, estPublique, dateC, idUtilisateur)
            VALUES (:nom, :description, :estPublique, NOW(), :idUtilisateur)
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':estPublique', $estPublique, PDO::PARAM_BOOL);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
        
        $stmt->execute();
        return intval($this->pdo->lastInsertId());
    }

    public function findByUser(string $userId): array {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE idUtilisateur = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getWatchlistContent(int $watchlistId): array {
        // 1. Récupérer les IDs de contenus liés à la watchlist
        $sql = "SELECT idContenu FROM " . DB_PREFIX . "contenircontenu WHERE idWatchlist = :watchlistId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $contentIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
        if (empty($contentIds)) {
            return []; // Aucun contenu trouvé pour la watchlist
        }
    
        // 2. Utiliser l'API pour récupérer les détails de chaque contenu
        $contenuDAO = new ContenuDAO($this->pdo); // Assurez-vous que cette classe gère l'accès à l'API
        $contents = [];
        foreach ($contentIds as $id) {
            $content = $contenuDAO->getContentFromTMDB($id); // Méthode pour récupérer les détails d'un contenu via l'API
            if ($content) {
                $contents[] = $content;
            }
        }
    
        return $contents;
    }
    

    public function addContenuToWatchlist(int $watchlistId, int $contenuId): bool {
        $sql = "INSERT INTO " . DB_PREFIX . "contenir (idWatchlist, idContenu) VALUES (:watchlistId, :contenuId)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':contenuId' => $contenuId
        ]);
    }

    public function removeContenuFromWatchlist(int $watchlistId, int $contenuId): bool {
        $sql = "DELETE FROM " . DB_PREFIX . "contenir WHERE idWatchlist = :watchlistId AND idContenu = :contenuId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':contenuId' => $contenuId
        ]);
    }

    public function getPublicWatchlists(): array {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE estPublique = true ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function hydrate(array $data): Watchlist {
        return new Watchlist(
            intval($data['idWatchlist']),
            $data['nom'],
            $data['description'],
            (bool) $data['estPublique'],
            new DateTime($data['dateC']),
            $data['idUtilisateur']
        );
    }
    
    public function hydrateAll(array $dataArray): array {
        $watchlists = [];
        foreach ($dataArray as $data) {
            $watchlists[] = $this->hydrate($data);
        }
        return $watchlists;
    }
}