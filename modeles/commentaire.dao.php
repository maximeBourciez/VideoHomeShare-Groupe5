<?php
class commentaireDAO {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    // get pdo
    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    // set pdo
    public function setPdo(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // Récupérer la moyenne et le total des notes pour un contenu
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

    // Récupérer les commentaires pour un collection
    public function getMoyenneEtTotalNotesCollection(string $idCollection): array {
        $sql = 'SELECT AVG(note) AS moyenne, COUNT(note) AS total 
                FROM vhs_commenterCollection 
                WHERE idCollection = :idCollection';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idCollection', $idCollection, PDO::PARAM_STR);
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

    // Récupérer les commentaires pour un contenu
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

        if ($result) {
            return $result;
        }

        return [];
    }
    
    //récupérer les commentaires pour une collection
    public function getCommentairesCollection(string $idCollection): array {
        $sql = 'SELECT idUtilisateur, titre, note, avis, estPositif
                FROM vhs_commenterCollection
                WHERE idCollection = :idCollection
                ORDER BY dateCommentaire DESC
                LIMIT 6';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idCollection', $idCollection, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            return $result;
        }

        return [];
    }


    // Créer un commentaire pour un contenu
    public function createCommentaireContenu(Commentaire $commentaire, string $idContenu) {
        $sql = 'INSERT INTO vhs_commenterContenu (idUtilisateur, titre, note, avis, estPositif, dateCommentaire, idContenu)
                VALUES (:idUtilisateur, :titre, :note, :avis, :estPositif, NOW(), :idContenu)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUtilisateur', $commentaire->getIdUtilisateur(), PDO::PARAM_STR);
        $stmt->bindParam(':titre', $commentaire->getTitre(), PDO::PARAM_STR);
        $stmt->bindParam(':note', $commentaire->getNote(), PDO::PARAM_INT);
        $stmt->bindParam(':avis', $commentaire->getAvis(), PDO::PARAM_STR);
        $stmt->bindParam(':estPositif', $commentaire->getEstPositif(), PDO::PARAM_BOOL);
        $stmt->bindParam(':idContenu', $idContenu, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Créer un commentaire pour une collection
    public function createCommentaireCollection(Commentaire $commentaire, string $idCollection) {
        $sql = 'INSERT INTO vhs_commenterCollection (idUtilisateur, titre, note, avis, estPositif, dateCommentaire, idCollection)
                VALUES (:idUtilisateur, :titre, :note, :avis, :estPositif, NOW(), :idCollection)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idUtilisateur', $commentaire->getIdUtilisateur(), PDO::PARAM_STR);
        $stmt->bindParam(':titre', $commentaire->getTitre(), PDO::PARAM_STR);
        $stmt->bindParam(':note', $commentaire->getNote(), PDO::PARAM_INT);
        $stmt->bindParam(':avis', $commentaire->getAvis(), PDO::PARAM_STR);
        $stmt->bindParam(':estPositif', $commentaire->getEstPositif(), PDO::PARAM_BOOL);
        $stmt->bindParam(':idCollection', $idCollection, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Hydrater un commentaire (Commentaire standard)
    public function hydrate(array $tableauAssaus): Commentaire {
        return new Commentaire($tableauAssaus['idUtilisateur'], $tableauAssaus['titre'], $tableauAssaus['note'], $tableauAssaus['avis'], $tableauAssaus['estPositif']);
    }

    // Hydrater tous les commentaires
    public function hydrateAll(array $tableauAssaus): ?array {
        $commentaires = [];
        foreach ($tableauAssaus as $row) {
            $commentaires[] = $this->hydrate($row);
        }
        return $commentaires;
    }
}
?>