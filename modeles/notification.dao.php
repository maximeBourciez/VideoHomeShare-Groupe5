<?php



class NotificationDAO {

    // Attiributs 
    /**
     * @var PDO|null $pdo Connexion à la BD
     */
    private ?PDO $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe NotificationDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getter
    /**
     * @brief Getter de la connexion à la base de données
     *
     * @return PDO|null Connexion à la base de données
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    // Setter
    /**
     * @brief Setter de la connexion à la base de données
     * 
     * @param PDO $pdo Connexion à la base de données
     * @return self
     */
    public function setPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }

    public function hydrate(array $row): Notification {
        $notification = new Notification();
        $notification->setIdNotification($row['id']);
        $notification->setContenu($row['contenu']);
        $notification->setDate(new DateTime($row['dateC']));
        return $notification;
    }

    public function hydrateAll(array $rows): array {
        $notifications = [];
        foreach($rows as $row) {
            $notification = $this->hydrate($row);
            $notifications[] = $notification;
        }
        return $notifications;
    }



    public function findbyUser(string $userId): array {
        $stmt = $this->getPdo()->prepare("SELECT * FROM ".DB_PREFIX."notification WHERE idUtilisateur = :idUser");
        $stmt->bindParam(":idUser", $userId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $notifications = $this->hydrateAll($result);
        return $notifications;
    }

    public function deleteById(int $idNotification, string $idUtilisateur): bool {
        $stmt = $this->getPdo()->prepare("DELETE FROM ".DB_PREFIX."notification WHERE id = :idNotification and idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idNotification", $idNotification);
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        return $stmt->execute();
    }

    public function creation(string $contenu , string $idutilisateur): bool {
        $stmt = $this->getPdo()->prepare("INSERT INTO ".DB_PREFIX."notification (contenu, idUtilisateur , dateC ) VALUES (:contenu, :idUser, now())");
        $stmt->bindParam(":contenu", $contenu);
        $stmt->bindParam(":idUser", $idutilisateur);
        return $stmt->execute();
    }

    public function delete(string $idUtilisateur): bool {
        $stmt = $this->getPdo()->prepare("DELETE FROM ".DB_PREFIX."notification WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        return $stmt->execute();
    }


}