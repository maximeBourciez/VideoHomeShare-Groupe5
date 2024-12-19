<?php

/**
 * @brief Classe d'accès aux données pour les commentaires
 * 
 * Cette classe gère toutes les opérations de lecture et d'écriture
 * des commentaires dans la base de données, incluant les notes et avis
 * sur les contenus et collections.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class CommentaireDAO {
    /** @var PDO|null Instance de connexion à la base de données */
    private ?PDO $pdo;

    /**
     * @brief Constructeur de CommentaireDAO
     * 
     * @param PDO|null $pdo Instance de connexion à la base de données
     */
    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère l'instance PDO
     * @return PDO|null L'instance de connexion
     */
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    /**
     * @brief Définit l'instance PDO
     * @param PDO $pdo Nouvelle instance de connexion
     */
    public function setPdo(PDO $pdo): void {
        $this->pdo = $pdo;
    }

    /**
     * @brief Récupère la moyenne et le total des notes pour un contenu
     * 
     * @param string $idContenuTmdb Identifiant TMDB du contenu
     * @return array Tableau avec la moyenne et le total des notes
     */
    public function getMoyenneEtTotalNotesContenu(string $idContenuTmdb): array {
        $sql = 'SELECT AVG(note) AS moyenne, COUNT(note) AS total 
                FROM vhs_commenterContenu 
                WHERE idContenuTmdb = :idContenuTmdb';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idContenuTmdb', $idContenuTmdb, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return [
                'moyenne' => isset($result['moyenne']) ? (double) round($result['moyenne'], 1) : 0,
                'total' => isset($result['total']) ? (int) $result['total'] : 0,
            ];
        }

        return [
            'moyenne' => 0,
            'total' => 0,
        ];
    }

    /**
     * @brief Récupère la moyenne et le total des notes pour une collection
     * 
     * @param string $idCollectionTmdb Identifiant TMDB de la collection
     * @return array Tableau avec la moyenne et le total des notes
     */
    public function getMoyenneEtTotalNotesCollection(string $idCollectionTmdb): array {
        $sql = 'SELECT AVG(note) AS moyenne, COUNT(note) AS total 
                FROM vhs_commenterCollection 
                WHERE idCollectionTmdb = :idCollectionTmdb';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idCollectionTmdb', $idCollectionTmdb, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return [
                'moyenne' => isset($result['moyenne']) ? (int) round($result['moyenne']) : 0,
                'total' => isset($result['total']) ? (int) $result['total'] : 0,
            ];
        }

        return [
            'moyenne' => 0,
            'total' => 0,
        ];
    }

    /**
     * @brief Récupère les commentaires pour un contenu
     * 
     * @param string $idContenuTmdb Identifiant TMDB du contenu
     * @return array Liste des commentaires
     */
    public function getCommentairesContenu(string $idContenuTmdb): array {
        $sql = 'SELECT idUtilisateur, titre, note, avis, estPositif
                FROM vhs_commenterContenu
                WHERE idContenuTmdb = :idContenuTmdb
                ORDER BY dateCommentaire DESC
                LIMIT 6';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idContenuTmdb', $idContenuTmdb, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    /**
     * @brief Récupère les commentaires pour une collection
     * 
     * @param string $idCollectionTmdb Identifiant TMDB de la collection
     * @return array Liste des commentaires
     */
    public function getCommentairesCollection(string $idCollectionTmdb): array {
        $sql = 'SELECT idUtilisateur, titre, note, avis, estPositif
                FROM vhs_commenterCollection
                WHERE idCollectionTmdb = :idCollectionTmdb
                ORDER BY dateCommentaire DESC
                LIMIT 6';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idCollectionTmdb', $idCollectionTmdb, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result ?: [];
    }

    /**
     * @brief Vérifie si un utilisateur a déjà commenté un contenu
     * 
     * @param string $idUtilisateur ID de l'utilisateur
     * @param string $idContenuTmdb ID du contenu TMDB
     * @return bool True si l'utilisateur a déjà commenté, false sinon
     */
    public function aDejaCommente(string $idUtilisateur, string $idContenuTmdb): bool {
        $sql = 'SELECT COUNT(*) as nbCommentaires 
                FROM vhs_commenterContenu 
                WHERE idUtilisateur = :idUtilisateur 
                AND idContenuTmdb = :idContenuTmdb';
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':idContenuTmdb', $idContenuTmdb, PDO::PARAM_STR);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['nbCommentaires'] > 0);
    }

    /**
     * @brief Crée un nouveau commentaire pour un contenu
     * 
     * @param Commentaire $commentaire Le commentaire à créer
     * @throws Exception Si l'utilisateur a déjà commenté
     */
    public function createCommentaireContenu(Commentaire $commentaire): void {
        // Vérification si l'utilisateur a déjà commenté
        if ($this->aDejaCommente($commentaire->getIdUtilisateur(), $commentaire->getIdContenuTmdb())) {
            throw new Exception("Désolé mais vous avez déjà commenté et noté ce film.");
        }

        $sql = 'INSERT INTO vhs_commenterContenu (idContenuTmdb, idUtilisateur, titre, note, avis, estPositif) 
                VALUES (:idContenuTmdb, :idUtilisateur, :titre, :note, :avis, :estPositif)';
        
        $stmt = $this->pdo->prepare($sql);
        
        $idContenuTmdb = $commentaire->getIdContenuTmdb();
        $idUtilisateur = $commentaire->getIdUtilisateur();
        $titre = $commentaire->getTitre();
        $note = $commentaire->getNote();
        $avis = $commentaire->getAvis();
        $estPositif = $commentaire->getEstPositif();
        
        $stmt->bindParam(':idContenuTmdb', $idContenuTmdb, PDO::PARAM_INT);
        $stmt->bindParam(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
        $stmt->bindParam(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindParam(':note', $note, PDO::PARAM_INT);
        $stmt->bindParam(':avis', $avis, PDO::PARAM_STR);
        $stmt->bindParam(':estPositif', $estPositif, PDO::PARAM_BOOL);
        $stmt->execute();
    }

    /**
     * @brief Hydrate un commentaire à partir d'un tableau de données
     * 
     * @param array $tableauAssaus Données du commentaire
     * @return Commentaire Le commentaire hydraté
     */
    public function hydrate(array $tableauAssaus): Commentaire {
        return new Commentaire(
            $tableauAssaus['idUtilisateur'],
            $tableauAssaus['titre'],
            $tableauAssaus['note'],
            $tableauAssaus['avis'],
            $tableauAssaus['estPositif']
        );
    }

    /**
     * @brief Hydrate plusieurs commentaires à partir d'un tableau de données
     * 
     * @param array $tableauAssaus Tableau de données des commentaires
     * @return array|null Liste des commentaires hydratés
     */
    public function hydrateAll(array $tableauAssaus): ?array {
        $commentaires = [];
        foreach ($tableauAssaus as $row) {
            $commentaires[] = $this->hydrate($row);
        }
        return $commentaires;
    }
}
?>