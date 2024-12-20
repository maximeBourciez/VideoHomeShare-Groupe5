<?php
class CollectionDAO {
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }
    // Lire une collection par ID
    public function getCollectionById(int $id): ?Collection {
        $sql = 'SELECT * FROM collections WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Collection(
                $result['id'],
                $result['titreCollection'],
                new DateTime($result['date']),
                $result['description'],
                $result['lienAffiche'],
                $result['nombreFilms']
            );
        }

        return null;
    }
}
?>