<?php 

// Classe MessageDAO
class MessageDAO{
    // Attiributs 
    private ?PDO $pdo; // Connexion à la BD

    // Constructeur
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getters
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    // Setters
    public function setPdo(PDO $pdo): self{
        $this->pdo = $pdo;
        return $this;
    }

    // Méthodes
    // Lister tous les messages d'un utilisateur par son id 
    public function listerMessagesParIdUser(int $id_user): array{
        $sql = "SELECT * FROM" . DB_PREFIX . "message WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Message');
        return $stmt->fetchAll();
    }

    // Récupérer les informations d'un message par son id
    public function getMessageParId(int $id): ?Message{
        $sql = "SELECT * FROM" . DB_PREFIX . "message WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Message');
        return $stmt->fetch();
    }

    // Ajouter un message 
    public function ajouterMessage(Message $message): bool{
        // Préparation de la requête
        $sql = "INSERT INTO" . DB_PREFIX . "message (valeur, nbLike, nbDislike, date, id_user, id_message_parent, id_fil) VALUES (:valeur, :nbLike, :nbDislike, :date, :id_user, :id_message_parent, :id_fil)";
        $stmt = $this->pdo->prepare($sql);

        // BindValues
        $stmt->bindValue(':valeur', $message->getValeur(), PDO::PARAM_STR);
        $stmt->bindValue(':nbLike', $message->getNbLike(), PDO::PARAM_INT);
        $stmt->bindValue(':nbDislike', $message->getNbDislike(), PDO::PARAM_INT);
        $stmt->bindValue(':date', $message->getDate(), PDO::PARAM_STR);
        $stmt->bindValue(':id_user', $message->getIdUser(), PDO::PARAM_INT);
        $stmt->bindValue(':id_message_parent', $message->getIdMessageParent(), PDO::PARAM_INT);
        $stmt->bindValue(':id_fil', $message->getIdFil(), PDO::PARAM_INT);
        
        // Execution de la requête
        return $stmt->execute();
    }
}