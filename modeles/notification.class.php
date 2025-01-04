<?php
/**
 * @class Notification
 * @brief Représente une notification dans le forum,  une notification de réponse, etc.
 * 
 * cette classe permet de manipuler les informations relatives à une notification, y compris les données associées
 */

class Notification {
    /**
     * @var int $idNotification
     * @brief Identifiant de la notification.
     */
     private ?int $idNotification;
     /**
      * @var string $contenu
      * @brief Contenu de la notification.
      */
     private ?string $contenu;
    /**
     * @var DateTime $date
     * @brief Date de création de la notification.
     */
    private ?DateTime  $date;
    /**
     * @brief Constructeur de la classe Notification
     * @param int $idNotification Identifiant de la notification
     * @param mixed $contenu Contenu de la notification
     * @param mixed $date Date de création de la notification
     */
    public function __construct(int $idNotification = null, ?string $contenu = null, ?DateTime $date = null) {
        $this->idNotification = $idNotification;
        $this->contenu = $contenu;
        $this->date = $date;
    }


    /**
     * @brief Getter de l'identifiant de la notification
     * @return int Identifiant de la notification
     */
    public function getIdNotification(): ?int {
        return $this->idNotification;
    }
    /**
     * @brief Getter du contenu de la notification
     * @return string Contenu de la notification
     */
    public function getcontenu(): ?string {
        return $this->contenu;
    }
    /**
     * @brief Getter de la date de création de la notification
     * @return DateTime Date de création de la notification
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }
    /**
     * @brief Setter de l'identifiant de la notification
     * @param int $idNotification Identifiant de la notification
     * @return void 
     */
    public function setIdNotification(int $idNotification): void {
        $this->idNotification = $idNotification;
    }
    /**
     * @brief Setter du contenu de la notification
     * @param string $contenu Contenu de la notification
     * @return void
     */
    public function setcontenu(string $contenu): void {
        $this->contenu = $contenu;
    }
    /**
     * @brief Setter de la date de création de la notification
     * @param DateTime $date Date de création de la notification
     * @return void
     */
    public function setDate(DateTime $date): void {
        $this->date = $date;
    }





}