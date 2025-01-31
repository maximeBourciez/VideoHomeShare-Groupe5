<?php

/**
 * @brief Classe d'accès aux données pour les watchlists
 */
class WatchlistDAO
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Méthode pour créer une nouvelle watchlist
     * 
     * @param string $nom Nom de la watchlist
     * @param string $description Description de la watchlist
     * @param bool $estPublique Statut public/privé de la watchlist
     * @param string $idUtilisateur Identifiant de l'utilisateur créateur
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
        $stmt->bindValue(':estPublique', $estPublique, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);

        $stmt->execute();
        return intval($this->pdo->lastInsertId());
    }

    /**
     * @brief Méthode pour modifier une watchlist
     * 
     * @param string $nom Nom de la watchlist
     * @param string $description Description de la watchlist
     * @param bool $estPublique Statut public/privé de la watchlist
     * @param string $idUtilisateur Identifiant de l'utilisateur créateur
     */
    public function update(int $idWatchlist, string $nom, string $description, bool $estPublique): bool
    {
        $sql = "UPDATE " . DB_PREFIX . "watchlist 
                SET nom = :nom, 
                    description = :description, 
                    estPublique = :estPublique 
                WHERE idWatchlist = :idWatchlist";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':idWatchlist' => $idWatchlist,
            ':nom' => $nom,
            ':description' => $description,
            ':estPublique' => (int)$estPublique // Explicit cast to integer
        ]);
    }

    /**
     * @brief Méthode pour récupérer les watchlists d'un utilisateur par son ID
     * 
     * @param int $id Identifiant de l'utilisateur
     * @return array Tableau d'objets Watchlist
     */
    public function findByUser(string $userId): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE idUtilisateur = :userId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @brief Méthode pour récupérer les contenus d'une watchlist
     * @param int $watchlistId Identifiant de la watchlist
     * @return array Tableau d'objets Contenu
     */
    public function getWatchlistContent(int $watchlistId): array
    {
        // 1. Récupérer les IDs de contenus liés à la watchlist
        $sql = "SELECT idContenuTmdb FROM " . DB_PREFIX . "contenirContenu WHERE idWatchlist = :watchlistId ORDER BY rang";
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

    /**
     * @brief Méthode pour ajouter un contenu à une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $contenuId Identifiant du contenu
     * 
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function addContenuToWatchlist(int $watchlistId, int $contenuId): bool {
        $sql = "SELECT MAX(rang) AS maximum FROM " . DB_PREFIX . "contenirContenu WHERE idWatchlist = (:watchlistId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $tab = $stmt->fetch();
        $max = $tab['maximum'];
        if ($max >= 1) {
            $rang = $max + 1;
        } else {
            $rang = 1;
        }
        
        $sql = "INSERT INTO " . DB_PREFIX . "contenirContenu (idWatchlist, idContenuTmdb, rang) VALUES (:watchlistId, :contenuId, :rang)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':contenuId' => $contenuId,
            ':rang' => $rang
        ]);
    }

    /**
     * @brief Méthode pour supprimer un contenu d'une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $contenuId Identifiant du contenu
     * 
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function removeContenuFromWatchlist(int $watchlistId, int $contenuId): bool {
        $sql = "DELETE FROM " . DB_PREFIX . "contenirContenu WHERE idWatchlist = :watchlistId AND idContenuTmdb = :contenuId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':contenuId' => $contenuId
        ]);
    }

    /**
     * @brief Méthode pour récupérer les watchlists publiques
     * 
     * @return array Tableau d'objets Watchlist
     */
    public function getPublicWatchlists(): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE estPublique = true ORDER BY date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @brief Méthode pour supprimer une watchlist
     * 
     * @param int $idWatchlist Identifiant de la watchlist à supprimer
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function delete(int $idWatchlist): bool {
        // Début de la transaction
        $this->pdo->beginTransaction();
        
        try {
            // Suppression des contenus associés dans la table de liaison
            $sqlContenu = "DELETE FROM " . DB_PREFIX . "contenircontenu WHERE idWatchlist = :idWatchlist";
            $stmtContenu = $this->pdo->prepare($sqlContenu);
            $stmtContenu->execute([':idWatchlist' => $idWatchlist]);
            
            // Suppression de la watchlist
            $sqlWatchlist = "DELETE FROM " . DB_PREFIX . "watchlist WHERE idWatchlist = :idWatchlist";
            $stmtWatchlist = $this->pdo->prepare($sqlWatchlist);
            $stmtWatchlist->execute([':idWatchlist' => $idWatchlist]);
            
            // Validation de la transaction
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            // En cas d'erreur, annulation de la transaction
            $this->pdo->rollBack();
            return false;
        }
    }

    /**
     * @brief Méthode pour hydrater un tableau de données en objet Watchlist
     * 
     * @param array $data Tableau de données
     * @return Watchlist|null Objet Watchlist ou null si non trouvé
     */
    private function hydrate(array $data): Watchlist
    {
        $watchlist = new Watchlist(
            intval($data['idWatchlist']),
            $data['nom'],
            $data['description'],
            (bool) $data['estPublique'],
            new DateTime($data['dateC']),
            $data['idUtilisateur']
        );

        // Récupérer et assigner les contenus
        $watchlist->setContenus($this->getWatchlistContent($data['idWatchlist']));

        return $watchlist;
    }

    /**
     * @brief Méthode pour hydrater un tableau de données en objets Watchlist
     * 
     * @param array $dataArray Tableau de données
     * @return array Tableau d'objets Watchlist
     */
    public function hydrateAll(array $dataArray): array
    {
        $watchlists = [];
        foreach ($dataArray as $data) {
            $watchlists[] = $this->hydrate($data);
        }
        return $watchlists;
    }

    /**
     * @brief Méthode pour vérifier si un contenu est dans une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $contenuId Identifiant du contenu
     * 
     * @return bool Vrai si le contenu est dans la watchlist, faux sinon
     */
    public function isContenuInWatchlist(int $watchlistId, int $contenuId): bool
    {
        $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "contenirContenu WHERE idWatchlist = :watchlistId AND idContenuTmdb = :contenuId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':contenuId' => $contenuId
        ]);
        return intval($stmt->fetchColumn()) > 0;
    }   
}

