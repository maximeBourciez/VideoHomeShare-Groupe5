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
    private int $idMessage;

    /**
     * @var string $valeur
     * @brief Contenu du message.
     */
    private string $valeur;

    /**
     * @var int $nblike
     * @brief Nombre de "likes" du message.
     */
    private int $nblike;

    /**
     * @var int $nbdislike
     * @brief Nombre de "dislikes" du message.
     */
    private int $nbdislike;

    /**
     * @var DateTime $dateC
     * @brief Date de création du message.
     */
    private DateTime $dateC;

    /**
     * @var int|null $idMessageParent
     * @brief Identifiant du message parent. Null si ce message n'est pas une réponse.
     */
    private ?int $idMessageParent;

    /**
     * @var Utilisateur $utilisateur
     * @brief L'utilisateur qui a écrit le message.
     */
    private Utilisateur $utilisateur;

    /**
     * @var Message|null $reponse
     * @brief Une réponse à ce message, si elle existe. Null si ce n'est pas une réponse.
     */
    private ?Message $reponse = null;

    /**
     * @brief Récupère l'identifiant du message.
     * @return int L'identifiant du message.
     */
    public function getIdMessage(): int {
        return $this->idMessage;
    }

    /**
     * @brief Définit l'identifiant du message.
     * @param int $idMessage L'identifiant du message.
     */
    public function setIdMessage(int $idMessage): void {
        $this->idMessage = $idMessage;
    }

    /**
     * @brief Récupère le contenu du message.
     * @return string Le contenu du message.
     */
    public function getValeur(): string {
        return $this->valeur;
    }

    /**
     * @brief Définit le contenu du message.
     * @param string $valeur Le contenu du message.
     */
    public function setValeur(string $valeur): void {
        $this->valeur = $valeur;
    }

    /**
     * @brief Récupère le nombre de likes du message.
     * @return int Le nombre de likes du message.
     */
    public function getNbLike(): int {
        return $this->nblike;
    }

    /**
     * @brief Définit le nombre de likes du message.
     * @param int $nblike Le nombre de likes du message.
     */
    public function setNbLike(int $nblike): void {
        $this->nblike = $nblike;
    }

    /**
     * @brief Récupère le nombre de dislikes du message.
     * @return int Le nombre de dislikes du message.
     */
    public function getNbDislike(): int {
        return $this->nbdislike;
    }

    /**
     * @brief Définit le nombre de dislikes du message.
     * @param int $nbdislike Le nombre de dislikes du message.
     */
    public function setNbDislike(int $nbdislike): void {
        $this->nbdislike = $nbdislike;
    }

    /**
     * @brief Récupère la date de création du message.
     * @return DateTime La date de création du message.
     */
    public function getDateC(): DateTime {
        return $this->dateC;
    }

    /**
     * @brief Définit la date de création du message.
     * @param DateTime $dateC La date de création du message.
     */
    public function setDateC(DateTime $dateC): void {
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
     * @return Message|null La réponse à ce message ou null si ce n'est pas une réponse.
     */
    public function getReponse(): ?Message {
        return $this->reponse;
    }

    /**
     * @brief Définit la réponse à ce message.
     * @param Message|null $reponse La réponse à ce message ou null si ce n'est pas une réponse.
     */
    public function setReponse(?Message $reponse): void {
        $this->reponse = $reponse;
    }
}
