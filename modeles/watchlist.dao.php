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
        $contents = [];

        // 1.1. Récupérer les IDs de contenus liés à la watchlist
        $sql = "SELECT idContenuTmdb, rang FROM " . DB_PREFIX . "contenirContenu WHERE idWatchlist = :watchlistId ORDER BY rang";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $contenusBd = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 1.2. Utiliser l'API pour récupérer les détails de chaque contenu
        $contenuDAO = new ContenuDAO($this->pdo); // Assurez-vous que cette classe gère l'accès à l'API
        foreach ($contenusBd as $contenu) {
            $content = $contenuDAO->getContentFromTMDB($contenu['idContenuTmdb']); // Méthode pour récupérer les détails d'un contenu via l'API
            if ($content) {
                $contents[$contenu['rang']] = $content;
            }
        }

        // 2.1 Récupérer les IDs de collections liées à la watchlist
        $sql = "SELECT idCollectionTMDB, rang FROM " . DB_PREFIX . "contenirCollection WHERE idWatchlist = :watchlistId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $collectionBd = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2.2. Utiliser la classe CollectionDAO pour récupérer les détails de chaque collection
        $collectionDAO = new CollectionDAO($this->pdo); // Assurez-vous que cette classe gère l'accès à la base de données
        foreach ($collectionBd as $collection) {
            $content = $collectionDAO->getCollectionFromTMDB($collection['idCollectionTMDB']); // Méthode pour récupérer les détails d'une collection
            if ($collection) {
                $contents[$collection['rang']] = $content;
            }
        }

        // 3.1 Récupérer les IDs de séries liées à la watchlist
        $sql = "SELECT idSerieTMDB, rang FROM " . DB_PREFIX . "contenirSerie WHERE idWatchlist = :watchlistId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $serieBd = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3.2. Utiliser la classe SerieDAO pour récupérer les détails de chaque série
        $serieDAO = new SerieDAO($this->pdo); // Assurez-vous que cette classe gère l'accès à la base de données
        foreach ($serieBd as $serie) {
            $content = $serieDAO->getSerieFromTMDB($serie['idSerieTMDB']); // Méthode pour récupérer les détails d'une série
            if ($serie) {
                $contents[$serie['rang']] = $content;
            }
        }

        // Trier les contenus par rang, maintenant clés
        ksort($contents);

        // Renvoyer seulement les valeurs une fois triées
        return array_values($contents);
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
        $rang = $this->trouverRang($watchlistId);
        
        $sql = "INSERT INTO " . DB_PREFIX . "contenirContenu (idWatchlist, idContenuTmdb, rang) VALUES (:watchlistId, :contenuId, :rang)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':contenuId' => $contenuId,
            ':rang' => $rang
        ]);
    }

    /**
     * @brief Méthode pour ajouter une collection à une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $collectionId Identifiant de la collection
     * 
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function addCollectionToWatchlist(int $watchlistId, int $collectionId): bool {
        $rang = $this->trouverRang($watchlistId);
        
        $sql = "INSERT INTO " . DB_PREFIX . "contenirCollection (idWatchlist, idCollectionTMDB, rang) VALUES (:watchlistId, :collectionId, :rang)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':collectionId' => $collectionId,
            ':rang' => $rang
        ]);
    }

    /**
     * @brief Méthode pour ajouter une série à une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $serieId Identifiant de la série
     * 
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function addSerieToWatchlist(int $watchlistId, int $serieId): bool {
        $rang = $this->trouverRang($watchlistId);
        
        $sql = "INSERT INTO " . DB_PREFIX . "contenirSerie (idWatchlist, idSerieTMDB, rang) VALUES (:watchlistId, :serieId, :rang)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':serieId' => $serieId,
            ':rang' => $rang
        ]);
    }

    public function trouverRang(int $watchlistId): int {
        // Récupérer le rang maximum dans les contenus
        $sql = "SELECT MAX(rang) AS maximum FROM " . DB_PREFIX . "contenirContenu WHERE idWatchlist = (:watchlistId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $tab = $stmt->fetch();
        $maxContenu = $tab['maximum'];

        // Récupérer le rang maximum dans les collections
        $sql = "SELECT MAX(rang) AS maximum FROM " . DB_PREFIX . "contenirCollection WHERE idWatchlist = (:watchlistId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $tab = $stmt->fetch();
        $maxCollec = $tab['maximum'];

        // Récupérer le rang maximum dans les séries
        $sql = "SELECT MAX(rang) AS maximum FROM " . DB_PREFIX . "contenirSerie WHERE idWatchlist = (:watchlistId)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':watchlistId' => $watchlistId]);
        $tab = $stmt->fetch();
        $maxSerie = $tab['maximum'];

        // Déterminer le rang du contenu à ajouter
        $rang = max($maxContenu, $maxCollec, $maxSerie) + 1;

        return $rang;
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
     * @brief Méthode pour supprimer une collection d'une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $collectionId Identifiant de la collection
     * 
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function removeCollectionFromWatchlist(int $watchlistId, int $collectionId): bool {
        $sql = "DELETE FROM " . DB_PREFIX . "contenirCollection WHERE idWatchlist = :watchlistId AND idCollectionTMDB = :collectionId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':collectionId' => $collectionId
        ]);
    }

    /**
     * @brief Méthode pour supprimer une série d'une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $serieId Identifiant de la série
     * 
     * @return bool Vrai si l'opération a réussi, faux sinon
     */
    public function removeSerieFromWatchlist(int $watchlistId, int $serieId): bool {
        $sql = "DELETE FROM " . DB_PREFIX . "contenirSerie WHERE idWatchlist = :watchlistId AND idSerieTMDB = :serieId";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':serieId' => $serieId
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

            // Suppression des collections associées dans la table de liaison
            $sqlCollection = "DELETE FROM " . DB_PREFIX . "contenirCollection WHERE idWatchlist = :idWatchlist";
            $stmtCollection = $this->pdo->prepare($sqlCollection);
            $stmtCollection->execute([':idWatchlist' => $idWatchlist]);

            // Suppression des séries associées dans la table de liaison
            $sqlSerie = "DELETE FROM " . DB_PREFIX . "contenirSerie WHERE idWatchlist = :idWatchlist";
            $stmtSerie = $this->pdo->prepare($sqlSerie);
            $stmtSerie->execute([':idWatchlist' => $idWatchlist]);
            
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

    /**
     * @brief Méthode pour vérifier si une collection est dans une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $collectionId Identifiant de la collection
     * 
     * @return bool Vrai si la collection est dans la watchlist, faux sinon
     */
    public function isCollectionInWatchlist(int $watchlistId, int $collectionId): bool
    {
        $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "contenirCollection WHERE idWatchlist = :watchlistId AND idCollectionTMDB = :collectionId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':collectionId' => $collectionId
        ]);
        return intval($stmt->fetchColumn()) > 0;
    }

    /**
     * @brief Méthode pour vérifier si une série est dans une watchlist
     * 
     * @param int $watchlistId Identifiant de la watchlist
     * @param int $serieId Identifiant de la série
     * 
     * @return bool Vrai si la série est dans la watchlist, faux sinon
     */
    public function isSerieInWatchlist(int $watchlistId, int $serieId): bool
    {
        $sql = "SELECT COUNT(*) FROM " . DB_PREFIX . "contenirSerie WHERE idWatchlist = :watchlistId AND idSerieTMDB = :serieId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':watchlistId' => $watchlistId,
            ':serieId' => $serieId
        ]);
        return intval($stmt->fetchColumn()) > 0;
    }
    
    /**
     * @brief Méthode pour récupérer les watchlists publiques
     * 
     * @return array Tableau d'objets Watchlist
     */
    public function getWatchlistPublic(): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "watchlist WHERE estPublique = 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}

