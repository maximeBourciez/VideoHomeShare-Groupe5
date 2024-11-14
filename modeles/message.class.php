<?php
/**
 * @file message.class.php
 * 
 * @brief Classe Message
 * 
 * @details Cette classe permet de gérer les messages postés sur le forum
 * 
 * @date 12/11/2024
 * 
 * @version 1.0
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */
class Message{
    // Attributs 
    /**
     * @var integer|null $id Identifiant du message
     */
    private ?int $id;
    /**
     * @var string|null $valeur Valeur du message
     */
    private ?string $valeur;
    /**
     * @var integer|null $nbLike Nombre de likes
     */
    private ?int $nbLike; 
    /**
     * @var integer|null $nbDislike Nombre de dislikes
     */
    private ?int $nbDislike;
    /**
     * @var string|null $date Date de publication
     */
    private ?DateTime $date;
    /**
     * @var Utilisateur $createur Utilisateur ayant posté le message
     */
    private ?Utilisateur $createur;
    /**
     * @var integer|null $id_message_parent Identifiant du message parent
     */
    private ?int $id_message_parent; 
    /**
     * @var Fil|null $fil Identifiant du fil dans lequel le message est posté
     */
    private ?Fil $fil;
    

    // Constructeur
    /**
     * @brief Constructeur de la classe Message
     * 
     * @param integer|null $id Identifiant du message
     * @param string|null $valeur Valeur du message
     * @param integer|null $nbLike Nombre de likes
     * @param integer|null $nbDislike Nombre de dislikes
     * @param string|null $date Date de publication
     * @param Utilisateur|null $user Utilisateur ayant posté le message
     * @param integer|null $id_message_parent Identifiant du message parent
     * @param integer|null $idFil Identifiant du fil dans lequel le message est posté
     */
    public function __construct(?int $id = null, ?string $valeur = null, ?int $nbLike = null, ?int $nbDislike = null, ?DateTime $date = null, ?Utilisateur $user = null, ?int $id_message_parent = null, ?int $idFil = null){
        $this->id = $id;
        $this->valeur = $valeur;
        $this->nbLike = $nbLike;
        $this->nbDislike = $nbDislike;
        $this->date = $date;
        $this->createur = $user;
        $this->id_message_parent = $id_message_parent;
        $this->fil = new Fil($idFil, "", new DateTime(), "");
    }


    // Encapsulation
    // Getters
    /**
     * @brief Getter de l'identifiant du message
     * @return integer|null Identifiant du message
     */
    public function getId(): ?int{
        return $this->id;
    }

    /**
     * @brief Getter de la valeur du message
     * @return string|null Valeur du message
     */
    public function getValeur(): ?string{
        return $this->valeur;
    }

    /**
     * @brief Getter du nombre de likes
     * @return integer|null Nombre de likes
     */
    public function getNbLike(): ?int{
        return $this->nbLike;
    }

    /**
     * @brief Getter du nombre de dislikes
     * @return integer|null Nombre de dislikes
     */
    public function getNbDislike(): ?int{
        return $this->nbDislike;
    }

    /**
     * @brief Getter de la date de publication
     * @return DateTime|null Date de publication
     */
    public function getDate(): ?DateTime{
        return $this->date;
    }

    /**
     * @brief Getter de l'utilisateur ayant posté le message
     * @return Utilisateur|null Utilisateur ayant posté le message
     */
    public function getUser(): ?Utilisateur{
        return $this->createur;
    }

    /**
     * @brief Getter de l'identifiant du message parent
     * @return integer|null Identifiant du message parent
     */
    public function getIdMessageParent(): ?int{
        return $this->id_message_parent;
    }

    /**
     * @brief Getter de l'identifiant du fil
     * @return integer|null Identifiant du fil
     */
    public function getIdFil(): ?int{
        return $this->fil->getId();
    }

    // Setters
    /**
     * @brief Setter de l'identifiant du message
     * @param integer $id Identifiant du message
     */
    public function setId(int $id){
        $this->id = $id;
    }

    /**
     * @brief Setter de la valeur du message
     * @param string $valeur Valeur du message
     */
    public function setValeur(string $valeur) {
        $this->valeur = $valeur;
    }

    /**
     * @brief Setter du nombre de likes
     * @param integer $nbLike Nombre de likes
     */
    public function setNbLike(int $nbLike) {
        $this->nbLike = $nbLike;
    }

    /**
     * @brief Setter du nombre de dislikes
     * @param integer $nbDislike Nombre de dislikes
     */
    public function setNbDislike(int $nbDislike){
        $this->nbDislike = $nbDislike;
    }

    /**
     * @brief Setter de la date de publication
     * @param DateTime $date Date de publication
     */
    public function setDate(DateTime $date){
        $this->date = $date;
    }

    /**
     * @brief Setter de l'utilisateur ayant posté le message
     * @param Utilisateur $user Utilisateur ayant posté le message
     */
    public function setCreateur(Utilisateur $user){
        $this->createur = $user;
    }

    /**
     * @brief Setter de l'identifiant du message parent
     * @param integer $id_message_parent Identifiant du message parent
     */
    public function setIdMessageParent(int $id_message_parent){
        $this->id_message_parent = $id_message_parent;
    }

    /**
     * @brief Setter de l'identifiant du fil
     * @param integer $id_fil Identifiant du fil
     */
    public function setIdFil(int $id_fil){
        $this->fil->setId($id_fil);
    }
}