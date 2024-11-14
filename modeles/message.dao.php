<?php 
/**
 * @file message.dao.php
 * 
 * @brief Classe MessageDAO
 * 
 * @details Classe permettant de gérer les accès à la base de données pour les messages
 * 
 * @date 12/11/2024
 * 
 * @version 1.0
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */
class MessageDAO{
    // Attiributs 
    /**
     * @var PDO|null $pdo Connexion à la BD
     */
    private ?PDO $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe MessageDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    public function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getter
    /**
     * @brief Getter de la connexion à la base de données
     *
     * @return PDO|null Connexion à la base de données
     */
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    // Setter
    /**
     * @brief Setter de la connexion à la base de données
     * 
     * @param PDO $pdo Connexion à la base de données
     * @return self
     */
    public function setPdo(PDO $pdo): self{
        $this->pdo = $pdo;
        return $this;
    }

    // Méthodes
    // Méthodes d'hydratation
    /**
     * @brief Méthode d'hydratation d'un message
     * 
     * @param array $row Ligne de la base de données
     * @return Message Objet Message hydraté
     */
    public function hydrate(array $row): Message{ 
        // Récupération de l'utilisateur
        $user = new Utilisateur();
        $user->setId($row['idUtilisateur']);
        $user->setPseudo($row['pseudo']);
        $user->setUrlImageProfil($row['urlImageProfil']);
        
        // Récupération des valeurs
        $id = $row['id'];
        $valeur = $row['valeur'];
        $nbLike = $row['nbLike'];
        $nbDislike = $row['nbDislike'];
        $date = $row['date'];
        $id_message_parent = $row['id_message_parent'];
        $id_fil = $row['idFil'];

        // Retourner le message
        return new Message($id, $valeur, $nbLike, $nbDislike, $date, $user, $id_message_parent, $id_fil);
    } 

    /**
     * @brief Méthode d'hydratation de tous les messages
     * 
     * @param array $rows Tableau de lignes de la base de données
     * @return array<Message> Tableau d'objets Message hydratés
     */
    function hydrateAll(array $rows): array{
        $messages = [];
        foreach($rows as $row){
            $message = $this->hydrate($row);
            array_push($messages, $message);  // Ajout du message au tableau 
        }
        return $messages;
    }


    // Méthodes de recherche
    /**
     * @brief Méthode permettant de lister tous les messages
     * 
     * @return array<Message> Tableau d'objets Message
     * 
     */
    public function listerMessages(): array{
        $sql = "SELECT * FROM" . DB_PREFIX . "message";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Méthode permettant de chercher un message par son id
     *
     * @note useless pour le moment
     *
     * @param integer $id
     * @return Message|null
     */
    public function chercherMessageParId(int $id): ?Message{
        $sql = "SELECT * FROM" . DB_PREFIX . "message WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrate($stmt->fetch());
    }


    
    /**
     * @brief Méthode permettant de lister les messages d'un utilisateur par id_user
     *
     * @details Méthode permettant de lister les messages d'un utilisateur par son id pour les lui afficher sur son profil
     * 
     * @param integer $id_user
     * @return array
     */
    public function listerMessagesParIdUser(int $id_user): array{
        $sql = "SELECT * FROM" . DB_PREFIX . "message WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Message');
        return $stmt->fetchAll();
    }

    /**
     * @brief Méthode d'ajout de message
     * 
     * @details Méthode permettant d'ajouter un message à la base de données à partir d'un objet Message passé en paramètre.
     *
     * @param Message $message
     * @return boolean
     */
    public function ajouterMessage(Message $message): bool{
        // Préparation de la requête
        $sql = "INSERT INTO" . DB_PREFIX . "message (valeur, nbLike, nbDislike, date, id_user, id_message_parent, id_fil) VALUES (:valeur, :nbLike, :nbDislike, :date, :id_user, :id_message_parent, :id_fil)";
        $stmt = $this->pdo->prepare($sql);

        // BindValues
        $stmt->bindValue(':valeur', $message->getValeur(), PDO::PARAM_STR);
        $stmt->bindValue(':nbLike', $message->getNbLike(), PDO::PARAM_INT);
        $stmt->bindValue(':nbDislike', $message->getNbDislike(), PDO::PARAM_INT);
        $stmt->bindValue(':date', $message->getDate(), PDO::PARAM_STR);
        $stmt->bindValue(':id_user', $message->g(), PDO::PARAM_INT);
        $stmt->bindValue(':id_message_parent', $message->getIdMessageParent(), PDO::PARAM_INT);
        $stmt->bindValue(':id_fil', $message->getIdFil(), PDO::PARAM_INT);

        // Execution de la requête
        return $stmt->execute();
    }

    /**
     * @brief Méthode de listing des messages par fil
     * 
     * @details Méthode permettant de lister les messages d'un fil de discussion par son id
     * 
     * @param integer $id_fil Identifiant du fil
     * 
     * @return array<Message> Tableau d'objets Message
     */
    public function listerMessagesParFil(int $id_fil): array {
    
        // Requête avec jointure pour récupérer les données des messages et des utilisateurs
        $sql = "
            SELECT 
                m.idMessage AS id, 
                m.valeur, 
                m.nbLike, 
                m.nbDislike, 
                m.dateC AS date, 
                m.idMessageParent AS id_message_parent, 
                m.idFil,
                u.idUtilisateur, 
                u.pseudo, 
                u.urlImageProfil
            FROM " . DB_PREFIX . "message m
            JOIN " . DB_PREFIX . "utilisateur u ON m.idUtilisateur = u.idUtilisateur
            WHERE m.idFil = :id_fil
        ";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_fil', $id_fil, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC); 
        return $this->hydrateAll($stmt->fetchAll());
    }
}