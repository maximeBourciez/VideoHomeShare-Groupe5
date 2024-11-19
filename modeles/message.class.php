<?php
/**
 * @class Message
 * @brief Représente un message dans le forum, qui peut être un message principal ou une réponse à un autre message.
 * 
 * Cette classe permet de manipuler les informations relatives à un message, y compris les données associées
 * telles que le texte du message, les likes/dislikes, la date de création, l'utilisateur qui a créé le message,
 * ainsi que les réponses éventuelles.
 */
class Message {
    /**
     * @var int $idMessage
     * @brief Identifiant unique du message.
     */
    private ?int $idMessage;

    /**
     * @var ?string $valeur
     * @brief Contenu du message.
     */
    private ?string $valeur;

    /**
     * @var DateTime $dateC
     * @brief Date de création du message.
     */
    private ?DateTime $dateC;

    /**
     * @var ?int $nbLikes
     * @brief Nombre de likes du message.
     */
    private ?int $nbLikes;

    /**
     * @var ?int $nbDislikes
     * @brief Nombre de dislikes du message.
     */
    private ?int $nbDislikes;

    /**
     * @var int|null $idMessageParent
     * @brief Identifiant du message parent. Null si ce message n'est pas une réponse.
     */
    private ?int $idMessageParent;

    /**
     * @var Utilisateur $utilisateur
     * @brief L'utilisateur qui a écrit le message.
     */
    private ?Utilisateur $utilisateur;

    /**
     * @var Message|null $reponse
     * @brief Une réponse à ce message, si elle existe. Null si ce n'est pas une réponse.
     */
    private ?array $reponses = [];


    // Constructeur
    /**
     * @brief Constructeur de la classe Message.
     * @param int $idMessage Identifiant du message.
     * @param string $valeur Contenu du message.
     * @param DateTime $dateC Date de création du message.
     * @param int|null $idMessageParent Identifiant du message parent. Null si ce message n'est pas une réponse.
     * @param Utilisateur $utilisateur L'utilisateur qui a écrit le message.
     * @param Message|null $reponse Une réponse à ce message, si elle existe. Null si ce n'est pas une réponse.
     */
    public function __construct(?int $idMessage = null, ?string $valeur = null, ?DateTime $dateC = null, ?int $nbLikes = null,?int $nbDislikes = null, ?int $idMessageParent = null, ?Utilisateur $utilisateur = null, ?array $reponse = []) {
        $this->idMessage = $idMessage;
        $this->valeur = $valeur;
        $this->dateC = $dateC;
        $this->nbLikes = $nbLikes;
        $this->nbDislikes = $nbDislikes;
        $this->idMessageParent = $idMessageParent;
        $this->utilisateur = $utilisateur;
        $this->reponses = $reponse;
    }


    // Encapsulation
    /**
     * @brief Récupère l'identifiant du message.
     * @return int L'identifiant du message.
     */
    public function getIdMessage(): ?int {
        return $this->idMessage;
    }

    /**
     * @brief Définit l'identifiant du message.
     * @param int $idMessage L'identifiant du message.
     */
    public function setIdMessage(?int $idMessage): void {
        $this->idMessage = $idMessage;
    }

    /**
     * @brief Récupère le contenu du message.
     * @return string Le contenu du message.
     */
    public function getValeur(): ?string {
        return $this->valeur;
    }

    /**
     * @brief Définit le contenu du message.
     * @param string $valeur Le contenu du message.
     */
    public function setValeur(?string $valeur): void {
        $this->valeur = $valeur;
    }

    /**
     * @brief Récupère le nombre de likes du message.
     * @return int|null Le nombre de likes du message.
     * 
     */
    public function getNbLikes(): ?int {
        return $this->nbLikes;
    }

    /**
     * @brief Définit le nombre de likes du message.
     * @param int|null $nbLikes Le nombre de likes du message.
     */
    public function setNbLikes(?int $nbLikes): void {
        $this->nbLikes = $nbLikes;
    }

    /**
     * @brief Récupère le nombre de dislikes du message.
     * @return int|null Le nombre de dislikes du message.
     */
    public function getNbDislikes(): ?int {
        return $this->nbDislikes;
    }

    /**
     * @brief Définit le nombre de dislikes du message.
     * @param int|null $nbDislikes Le nombre de dislikes du message.
     */
    public function setNbDislikes(?int $nbDislikes): void {
        $this->nbDislikes = $nbDislikes;
    }

    /**
     * @brief Récupère la date de création du message.
     * @return DateTime La date de création du message.
     */
    public function getDateC(): ?DateTime {
        return $this->dateC;
    }

    /**
     * @brief Définit la date de création du message.
     * @param DateTime $dateC La date de création du message.
     */
    public function setDateC(?DateTime $dateC): void {
        $this->dateC = $dateC;
    }

    /**
     * @brief Récupère l'identifiant du message parent. 
     *        Si ce message n'est pas une réponse, retourne null.
     * @return int|null L'identifiant du message parent ou null si ce n'est pas une réponse.
     */
    public function getIdMessageParent(): ?int {
        return $this->idMessageParent;
    }

    /**
     * @brief Définit l'identifiant du message parent. 
     *        Si ce message n'est pas une réponse, définit à null.
     * @param int|null $idMessageParent L'identifiant du message parent ou null si ce n'est pas une réponse.
     */
    public function setIdMessageParent(?int $idMessageParent): void {
        $this->idMessageParent = $idMessageParent;
    }

    /**
     * @brief Récupère l'utilisateur qui a créé ce message.
     * @return Utilisateur L'utilisateur qui a créé le message.
     */
    public function getUtilisateur(): Utilisateur {
        return $this->utilisateur;
    }

    /**
     * @brief Définit l'utilisateur qui a créé ce message.
     * @param Utilisateur $utilisateur L'utilisateur qui a créé le message.
     */
    public function setUtilisateur(Utilisateur $utilisateur): void {
        $this->utilisateur = $utilisateur;
    }

    /**
     * @brief Récupère la réponse à ce message, si elle existe.
     * @return array<Message>|null La réponse à ce message ou null si ce n'est pas une réponse.
     */
    public function getReponses(): ?array {
        return $this->reponses;
    }

    /**
     * @brief Définit la réponse à ce message.
     * @param array<Message>|null $reponse La réponse à ce message ou null si ce n'est pas une réponse.
     */
    public function setReponse(?array $reponses): void {
        $this->reponses = $reponses;
    }

    /**
     * @brief Ajoute une réponse à ce message.
     * @param Message $reponse La réponse à ajouter.
     */
    public function addReponse(Message $reponse): void {
        $this->reponses[] = $reponse;
    }

    /** 
     * @brief Vérifie si la réponse existe dans ce message.
     * @param Message $reponse La réponse à vérifier.
     */
    public function hasReponse(Message $reponse): bool {
        foreach ($this->reponses as $reponse) {
            if ($reponse->getIdMessage() === $reponse->getIdMessage()) {
                return true;
            }
        }
        return false;
    }
}
