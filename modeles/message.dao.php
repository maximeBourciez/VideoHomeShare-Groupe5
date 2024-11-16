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
class MessageDAO
{
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

    // Méthodes
    // Méthodes d'hydratation
    /**
     * @brief Méthode d'hydratation d'un message
     * 
     * @param array $row Ligne de la base de données
     * @return Message Objet Message hydraté
     */
    public function hydrate(array $row): Message
    {
        $message = new Message();
        $message->setIdMessage($row['idMessage']);
        $message->setValeur($row['valeur']);
        $message->setDateC(new DateTime($row['dateC']));
        $message->setIdMessageParent($row['idMessageParent']);

        // Hydratation de l'utilisateur du message parent
        $user = new Utilisateur();
        $user->setId($row['auteur_id']);
        $user->setPseudo($row['auteur_pseudo']);
        $user->setUrlImageProfil($row['auteur_urlImageProfil']);
        $message->setUtilisateur($user);

        // Ajouter une réponse s'il y en a
        if ($row['reponse_valeur']) {
            $reponse = new Message();
            $reponse->setValeur($row['reponse_valeur']);
            $reponse->setDateC(new DateTime($row['reponse_dateC']));

            // Hydratation de l'utilisateur de la réponse
            $reponseUser = new Utilisateur();
            $reponseUser->setId($row['reponse_utilisateur_id']); // Modifié ici
            $reponseUser->setPseudo($row['reponse_pseudo'] ?? 'Utilisateur inconnu');
            $reponseUser->setUrlImageProfil($row['reponse_urlImageProfil']);
            $reponse->setUtilisateur($reponseUser);

            // Assigner la réponse au message
            $message->setReponse($reponse);
        }

        return $message;
    }

    /**
     * @brief Méthode d'hydratation de tous les messages
     * 
     * @param array $rows Tableau de lignes de la base de données
     * @return array<Message> Tableau d'objets Message hydratés
     */
    function hydrateAll(array $rows): array
    {
        $hydratedMessages = [];
        foreach ($rows as $row) {
            // Hydratation d'un message parent
            $message = $this->hydrate($row);
            $hydratedMessages[] = $message;
        }

        return $hydratedMessages;
    }


    // Méthodes de recherche
    /**
     * @brief Méthode permettant de lister tous les messages
     * 
     * @return array<Message> Tableau d'objets Message
     * 
     */
    public function listerMessages(): array
    {
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
    public function chercherMessageParId(int $id): ?Message
    {
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
    public function listerMessagesParIdUser(int $id_user): array
    {
        $sql = "SELECT * FROM" . DB_PREFIX . "message WHERE id_user = :id_user";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Message');
        return $stmt->fetchAll();
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
    public function listerMessagesParFil(int $idFil): array
    {
        $sql = "
    WITH MessageThread AS (
        SELECT
            m.*,
            COALESCE(m.idMessageParent, m.idMessage) as thread_id,
            u.idUtilisateur as auteur_id,
            u.pseudo as auteur_pseudo,
            u.urlImageProfil as auteur_urlImageProfil
        FROM " . DB_PREFIX . "message m
        INNER JOIN " . DB_PREFIX . "utilisateur u ON m.idUtilisateur = u.idUtilisateur
        WHERE m.idFil = :idFil
        )
    SELECT
        m1.*,
        m1.auteur_id,
        m1.auteur_pseudo,
        m1.auteur_urlImageProfil,
        m2.valeur AS reponse_valeur,
        m2.dateC AS reponse_dateC,
        m2.auteur_id AS reponse_utilisateur_id,
        u2.pseudo AS reponse_pseudo,
        u2.urlImageProfil AS reponse_urlImageProfil
    FROM MessageThread m1
    LEFT JOIN MessageThread m2 ON m1.idMessage = m2.idMessageParent
    LEFT JOIN " . DB_PREFIX . "utilisateur u2 ON m2.auteur_id = u2.idUtilisateur
    ORDER BY m1.thread_id ASC, m1.dateC ASC;
    ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idFil', $idFil, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $messages = $stmt->fetchAll();
        return $this->hydrateAll($messages);
    }
    
}
