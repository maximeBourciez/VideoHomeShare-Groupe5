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

    // Setters
    public function setId(int $id): self{
        $this->id = $id;
        return $this;
    }

    public function setValeur(string $valeur): self{
        $this->valeur = $valeur;
        return $this;
    }

    public function setNbLike(int $nbLike): self{
        $this->nbLike = $nbLike;
        return $this;
    }

    public function setNbDislike(int $nbDislike): self{
        $this->nbDislike = $nbDislike;
        return $this;
    }

    public function setDate(string $date): self{
        $this->date = $date;
        return $this;
    }

    public function setIdUser(int $id_user): self{
        $this->id_user = $id_user;
        return $this;
    }

    public function setIdMessageParent(int $id_message_parent): self{
        $this->id_message_parent = $id_message_parent;
        return $this;
    }


}