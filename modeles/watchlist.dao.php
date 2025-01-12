<?php

class WatchlistDAO {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    // Méthodes existantes...

    public function createWatchlist(Watchlist $watchlist): ?int {
        $sql = "INSERT INTO " . DB_PREFIX . "watchlist (nom, description, estPublique, date, idUtilisateur) 
                VALUES (:nom, :description, :estPublique, NOW(), :idUtilisateur)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $watchlist->getNom(),
            ':description' => $watchlist->getDesc(),
            ':estPublique' => $watchlist->getPublicite(),
            ':idUtilisateur' => $watchlist->getIdUtilisateur()
        ]);

        return $this->pdo->lastInsertId();
    }

    public function findByUser(int $userId): array {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE idUtilisateur = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getWatchlistContent(int $watchlistId): array {
        $sql = "SELECT c.* FROM " . DB_PREFIX . "contenu c 
                JOIN " . DB_PREFIX . "contenir co ON c.idContenu = co.idContenu 
                WHERE co.idWatchlist = :watchlistId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        
        $contenuDAO = new ContenuDAO($this->pdo);
        return $contenuDAO->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
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

    public function getFavoris(int $userId): array {
        $sql = "SELECT c.* FROM " . DB_PREFIX . "contenu c 
                JOIN " . DB_PREFIX . "favori f ON c.idContenu = f.idContenu 
                WHERE f.idUtilisateur = :userId";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        
        $contenuDAO = new ContenuDAO($this->pdo);
        return $contenuDAO->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function getPublicWatchlists(): array {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE estPublique = true ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}