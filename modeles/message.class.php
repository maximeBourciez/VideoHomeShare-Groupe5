<?php

// Classe Message
class Message{
    // Attributs 
    private ?int $id; // Identifiant
    private ?string $valeur; // Valeur du message
    private ?int $nbLike; // Nombre de likes
    private ?int $nbDislike; // Nombre de dislikes
    private ?string $date; // Date de publication
    private ?int $id_user; // Identifiant de l'utilisateur ayant posté le message
    private ?int $id_message_parent; // Identifiant du message parent
    private ?int $id_fil; // Identifiant du fil dans lequel le message est posté

    // Constructeur
    public function __construct(?int $id, ?string $valeur, ?int $nbLike, ?int $nbDislike, ?string $date, ?int $id_user){
        $this->id = $id;
        $this->valeur = $valeur;
        $this->nbLike = $nbLike;
        $this->nbDislike = $nbDislike;
        $this->date = $date;
        $this->id_user = $id_user;
    }


    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }

    public function getValeur(): ?string{
        return $this->valeur;
    }

    public function getNbLike(): ?int{
        return $this->nbLike;
    }

    public function getNbDislike(): ?int{
        return $this->nbDislike;
    }

    public function getDate(): ?string{
        return $this->date;
    }

    public function getIdUser(): ?int{
        return $this->id_user;
    }

    public function getIdMessageParent(): ?int{
        return $this->id_message_parent;
    }

    public function getIdFil(): ?int{
        return $this->id_fil;
    }


    // Setters
    public function setId(int $id){
        $this->id = $id;
    }

    public function setValeur(string $valeur) {
        $this->valeur = $valeur;
    }

    public function setNbLike(int $nbLike) {
        $this->nbLike = $nbLike;
    }

    public function setNbDislike(int $nbDislike){
        $this->nbDislike = $nbDislike;
    }

    public function setDate(string $date){
        $this->date = $date;
    }

    public function setIdUser(int $id_user){
        $this->id_user = $id_user;
    }

    public function setIdMessageParent(int $id_message_parent){
        $this->id_message_parent = $id_message_parent;
    }

    public function setIdFil(int $id_fil){
        $this->id_fil = $id_fil;
    }

}