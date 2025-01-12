<?php

/**
 * @file notification.dao.php
 * 
 * @brief Notification Data Access Object
 * 
 * @details Data Access Object de la classe Notification
 * 
 * @version 1.0
 * 
 * @date 30/12/2024
 * 
 * @author Sylvain Trouilh
 */


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
    /**
     * @brief Méthode d'hydratation d'une notification
     * @param array $row Ligne de la base de données
     * @return Notification Objet Notfication hydraté
     */
    public function hydrate(array $row): Notification {
        $notification = new Notification();
        $notification->setIdNotification($row['id']);
        $notification->setContenu($row['contenu']);
        $notification->setDate(new DateTime($row['dateC']));
        return $notification;
    }
    /**
     * @brief Méthode d'hydratation de tous les notifications
     *
     * @param array $rows Tableau de lignes récupérées de la base de données
     * @return array<Notification> Tableau d'objets Notification hydratés
     */
    public function hydrateAll(array $rows): array {
        $notifications = [];
        foreach($rows as $row) {
            $notification = $this->hydrate($row);
            $notifications[] = $notification;
        }
        return $notifications;
    }


    /**
     * @brief Méthode de recherche d'une notification par son id utilisateur
     * @param string $userId Id de l'utilisateur
     * @return array Tableau d'objets Notification
     */
    public function findbyUser(string $userId): array {
        $stmt = $this->getPdo()->prepare("SELECT * FROM ".DB_PREFIX."notification WHERE idUtilisateur = :idUser");
        $stmt->bindParam(":idUser", $userId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $notifications = $this->hydrateAll($result);
        return $notifications;
    }

    /**
     * @brief Méthode de suppression d'une notification par son idantifiant  et l'id de l'utilisateur
     * @param int $idNotification idantifiant de la notification
     * @param string $idUtilisateur id de l'utilisateur
     * @return bool true si la suppression a été effectuée, false sinon
     */
    public function deleteById(int $idNotification, string $idUtilisateur): bool {
        $stmt = $this->getPdo()->prepare("DELETE FROM ".DB_PREFIX."notification WHERE id = :idNotification and idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idNotification", $idNotification);
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        return $stmt->execute();
    }
    /**
     * @brief Méthode de création d'une notification
     * @param string $contenu Contenu de la notification
     * @param string $idutilisateur Id de l'utilisateur
     * @return bool true si la création a été effectuée, false sinon
     */
    public function creation(string $contenu , string $idutilisateur): bool {
        $stmt = $this->getPdo()->prepare("INSERT INTO ".DB_PREFIX."notification (contenu, idUtilisateur , dateC ) VALUES (:contenu, :idUser, now())");
        $stmt->bindParam(":contenu", $contenu);
        $stmt->bindParam(":idUser", $idutilisateur);
        return $stmt->execute();
    }
    /**
     * @brief Méthode de suppression de toutes les notifications d'un utilisateur
     * @param string $idUtilisateur Id de l'utilisateur
     * @return bool true si la suppression a été effectuée, false sinon
     */
    public function delete(string $idUtilisateur): bool {
        $stmt = $this->getPdo()->prepare("DELETE FROM ".DB_PREFIX."notification WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idUtilisateur", $idUtilisateur);
        return $stmt->execute();
    }


}