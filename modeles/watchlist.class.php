<?php

class Watchlist{
    // Attributs 
    private ?int $id; //Identifiant de la watchlist
    private ?string $nom; //Nom de la watchlist
    private ?string $desc; //Description de la watchlist
    private ?bool $estPublique; //Indicateur de publicité d'une WL
    private ?string $date; //Date de création de la WL

    // Constructeur
    public function __construct(?int $id = null, ?string $nom = null, ?string $desc = null, ?bool $estPublique = null, ?string $date = null){
        $this->id = $id;
        $this->nom = $nom;
        $this->desc = $desc;
        $this->estPublique = $estPublique;
        $this->date = $date;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getNom(): ?string{
        return $this->nom;
    }
    public function getDesc(): ?string{
        return $this->desc;
    }
    public function getPublicite(): ?bool{
        return $this->estPublique;
    }
    public function getDate(): ?string{
        return $this->date;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setNom(?string $nom) : void{
        $this->nom = $nom;
    }
    public function setDesc(?string $desc) : void{
        $this->desc = $desc;
    }
    public function setPublicite(?bool $estPublique) : void{
        $this->estPublique = $estPublique;
    }
    public function setDate(?string $date) : void{
        $this->date = $date;
    }
}