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

    /**
     * @brief Méthode permettant de récupérer le nombre de messages d'un fil de discussion
     * 
     * @param mixed $idFil Identifiant du fil de discussion
     * 
     * @return mixed Nombre de messages
     */
    public function getNombreMessages($idFil) : ?int
    {
        $requete = "SELECT COUNT(*) as total 
                   FROM vhs_message 
                   WHERE idFil = :id_fil;";

        $stmt = $this->pdo->prepare($requete);
        $stmt->bindValue(':id_fil', $idFil, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
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

        // Gestion sécurisée de la date
        $message->setDateC(!empty($row['dateC']) ? new DateTime($row['dateC']) : null);

        $message->setIdMessageParent($row['idMessageParent']);
        $message->setIdFil($row['idFil']);
        if (isset($row['like_count'])) {
            $message->setNbLikes($row['like_count']);
        }
        if (isset($row['dislike_count'])) {
            $message->setNbDislikes($row['dislike_count']);
        }


        // Hydratation de l'utilisateur associé
        $user = new Utilisateur();
        $user->setId($row['idUtilisateur']);
        $user->setPseudo($row['pseudo']);
        $user->setUrlImageProfil($row['urlImageProfil']);
        $message->setUtilisateur($user);

        return $message;
    }
    /**
     * @brief Méthode d'hydratation de tous les messages avec gestion des réponses
     *
     * @param array $rows Tableau de lignes récupérées de la base de données
     * @return array<Message> Tableau d'objets Message hydratés, avec réponses associées
     */
    public function hydrateAll(array $rows): array
    {
        $messages = []; // Tous les messages indexés par leur ID
        $messagesParents = []; // Tableau pour stocker uniquement les messages principaux

        // Hydrater tous les messages et les indexer par ID
        foreach ($rows as $row) {
            $message = $this->hydrate($row);
            $messages[$message->getIdMessage()] = $message;
        }

        // Lier les réponses à leurs parents
        foreach ($messages as $message) {
            if ($message->getIdMessageParent() === null) {
                // Message sans parent -> c'est un message principal
                $messagesParents[] = $message;
            } else {
                // Message avec parent -> ajouter comme réponse
                $parentId = $message->getIdMessageParent();
                if (isset($messages[$parentId])) {
                    $messages[$parentId]->addReponse($message);
                } else {
                    // Si le parent n'existe pas, on ajoute le message comme message principal
                    $messagesParents[] = $message;
                }
            }
        }

        // Retourner uniquement les messages principaux (avec les réponses déjà attachées)
        return $messagesParents;
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
        $sql = "SELECT * FROM " . DB_PREFIX . "message  WHERE idMessage = :id";
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
    public function listerMessagesParIdUser(?string $id_user): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "message M join " . DB_PREFIX . "utilisateur U ON  M.idUtilisateur=U.idUtilisateur WHERE M.idUtilisateur = :id_user ORDER BY dateC DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id_user', $id_user, PDO::PARAM_STR);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $messages = $stmt->fetchAll();

        return ($this->hydrateAll($messages));
    }

    /**
     * @brief Méthode permettant de récupérer les messages d'un fil de discussion
     * 
     * @param mixed $idFil Identifiant du fil de discussion
     * @param mixed $page Numéro de la page
     * @param mixed $messagesParPage Nombre de messages par page
     * 
     * @return array<Message> Tableau d'objets Message
     */
    public function getMessagesParentsPagines($idFil, $page = 1, $messagesParPage = 5): ?array
    {
        $offset = ($page - 1) * $messagesParPage;

        // Récupérer les messages parents 
        $requete = "
                   SELECT m.*, 
                        u.*, 
                        ld1.like_count, 
                        ld1.dislike_count
                FROM " . DB_PREFIX . "message m
                LEFT JOIN (
                    SELECT idMessage,
                            SUM(CASE WHEN reaction = true THEN 1 ELSE 0 END) AS like_count,
                            SUM(CASE WHEN reaction = false THEN 1 ELSE 0 END) AS dislike_count
                    FROM " . DB_PREFIX . "reagir
                    GROUP BY idMessage
                ) AS ld1 ON m.idMessage = ld1.idMessage
                LEFT JOIN " . DB_PREFIX . "utilisateur u ON m.idUtilisateur = u.idUtilisateur
                WHERE m.idFil = :idFil
                AND idMessageParent IS NULL 
                   ORDER BY dateC DESC 
                   LIMIT :limit OFFSET :offset;";

        $stmt = $this->pdo->prepare($requete);
        $stmt->bindValue(':idFil', $idFil, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $messagesParPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $messages = $this->hydrateAll($stmt->fetchAll());

        // Récupérer les enfants de chaque message parent
        foreach ($messages as $message) {
            $message->setReponses($this->getMessagesEnfants($message->getIdMessage()));
        }

        return $messages;
    }

    /**
     * @brief Méthode permettant de récupérer les messages enfants d'un message parent
     * 
     * @details Méthode permettant de récupérer les messages enfants d'un message parent récursivemement, c'est-à-dre qu'on récupère l'intégralité de la discussion sous-jascente
     * 
     * @param mixed $idMessageParent Identifiant du message parent
     * 
     * @return array<Message> Tableau d'objets Message
     * 
     * @warning Fonction à appels récursifs
     */
    private function getMessagesEnfants($idMessageParent): ?array
    {
        $requete = "
                   SELECT m.*, 
                        u.*, 
                        ld1.like_count, 
                        ld1.dislike_count
                FROM " . DB_PREFIX . "message m
                LEFT JOIN (
                    SELECT idMessage,
                            SUM(CASE WHEN reaction = true THEN 1 ELSE 0 END) AS like_count,
                            SUM(CASE WHEN reaction = false THEN 1 ELSE 0 END) AS dislike_count
                    FROM " . DB_PREFIX . "reagir
                    GROUP BY idMessage
                ) AS ld1 ON m.idMessage = ld1.idMessage
                LEFT JOIN " . DB_PREFIX . "utilisateur u ON m.idUtilisateur = u.idUtilisateur
                WHERE m.idMessageParent = :idMessageParent
                ORDER BY dateC DESC;";
    
        $stmt = $this->pdo->prepare($requete);
        $stmt->bindValue(':idMessageParent', $idMessageParent, PDO::PARAM_INT);
        $stmt->execute();
    
        $enfants = $this->hydrateAll($stmt->fetchAll());
    
        // Récupérer les enfants de chaque enfant de manière récursive
        foreach ($enfants as $enfant) {
            $enfant->setReponses($this->getMessagesEnfants($enfant->getIdMessage()));
        }
    
        return $enfants;
    }


    /**
     * @brief Méthode d'ajout d'une reponse dans un fil de discussion
     * 
     * @param int|null $idFil Identifiant du fil
     * @param int|null $idMessageParent Identifiant du message parent
     * @param string|null $message Contenu du message
     * @param string|null $idUser Identifiant de l'utilisateur
     * 
     * @return void
     */
    public function ajouterMessage(?int $idFil, ?int $idMessageParent, ?string $message, ?string $idUser): void
    {
        // Requête d'insertion
        $sql = "INSERT INTO " . DB_PREFIX . "message ( valeur, dateC, idMessageParent, idFil, idUtilisateur) VALUES ( :valeur, NOW(), :idMessageParent, :idFil, :idUtilisateur)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idFil', $idFil, PDO::PARAM_INT);
        $stmt->bindValue(':idMessageParent', $idMessageParent, PDO::PARAM_INT);
        $stmt->bindValue(':valeur', $message, PDO::PARAM_STR);
        $stmt->bindValue(':idUtilisateur', $idUser, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * @brief Méthode d'ajout d'une réaction à un message
     * 
     * @details Méthode permettant d'ajouter une réaction à un message. AJout d'un like ou d'un dislike selon la valeur de $reaction. Si jamais l'utilisateur a déjà réagi, la réaction est mise à jour.
     * 
     * @param int $idMessage Identifiant du message
     * @param string $idUtilisateur Identifiant de l'utilisateur
     * @param bool $reaction Réaction (true = like, false = dislike)
     * 
     */
    public function ajouterReaction(int $idMessage, string $idUtilisateur, bool $reaction): void
    {
        // Préparer la requête SQL
        $sql = "INSERT INTO " . DB_PREFIX . "reagir (idMessage, idUtilisateur, reaction) 
                VALUES (:idMessage, :idUtilisateur, :reaction) 
                ON DUPLICATE KEY UPDATE reaction = :reaction";

        $stmt = $this->pdo->prepare($sql);

        // Lier les valeurs
        $stmt->bindValue(':idMessage', $idMessage, PDO::PARAM_INT);
        $stmt->bindValue(':idUtilisateur', $idUtilisateur, PDO::PARAM_STR);
        $stmt->bindValue(':reaction', $reaction ? 1 : 0, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();

    }

    /**
     * Méthode qui retourne si le message appartient bien à l'utilisateur
     * 
     * @param int|null $idMessage Message à supprimer
     * @param string|null $idUser Identifiant de l'utilisateur qui veut supprimer le message
     * 
     * @return bool 
     */
    public function checkProprieteMessage(?int $idMessage, ?string $idUser)
    {
        // Préparer la requête
        $sql = "SELECT * FROM " . DB_PREFIX . "message M
                WHERE idMessage = :idMessage
                AND idUtilisateur = :idUser";

        $stmt = $this->pdo->prepare($sql);

        // Lier les valeurs
        $stmt->bindValue(':idMessage', $idMessage, PDO::PARAM_INT);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_STR);

        // Exécuter la requête
        $stmt->execute();

        // Récupérer le résultat
        $count = $stmt->fetchColumn();

        // Retourner vrai si le message appartient à l'utilisateur
        return $count > 0;
    }

    /**
     * Méthode de suppression d'un message
     * 
     * @param int|null $idMessage identifiant du message à supprimer
     * 
     * @return void
     */
    public function supprimerMessage(?int $idMessage): void
    {
        // Préparer la requête SQL
        $sql = "UPDATE " . DB_PREFIX . "message
            SET valeur = :valeurMessageSupprime, 
                idUtilisateur = :valeurUtilisateurSupprime
            WHERE idMessage = :idMessage";

        // Préparer la requête
        $stmt = $this->pdo->prepare($sql);

        // Lier les valeurs
        $stmt->bindValue(':valeurMessageSupprime', VALEUR_MESSAGE_SUPPRIME, PDO::PARAM_STR);
        $stmt->bindValue(':valeurUtilisateurSupprime', VALEUR_UTILISATEUR_MESSAGE_SUPPRIME, PDO::PARAM_NULL);
        $stmt->bindValue(':idMessage', $idMessage, PDO::PARAM_INT);

        // Exécuter la requête
        $stmt->execute();
    }

    /**
     * Méthode permettant de supprimer toutes les réactions à un message lors de sa suppression
     * 
     * @param int|null $idMessage Identifiant du message dont les réactions sont à purger
     * 
     * @return void
     */
    public function purgerReactions(?int $idMessage): void
    {
        // Préparer la requête
        $sql = "DELETE FROM " . DB_PREFIX . "reagir
               WHERE idMessage = :idMessage";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idMessage', $idMessage);
        $stmt->execute();
    }


    /**
     * Méthode permettant de trouver l'auteur d'un message
     * 
     * @param int|null $idMessage Identifiant du message
     * 
     * @return string|null Identifiant de l'utilisateur
     */
    public function findAuthor(?int $idMessage): ?string
    {
        // Préparer la requête
        $sql = "SELECT idUtilisateur FROM " . DB_PREFIX . "message
                WHERE idMessage = :idMessage";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idMessage', $idMessage, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchColumn();
    }
}
