<?php

class Theme{
    // Attributs 
    private ?int $id; //Identifiant du theme
    private ?string $nom; //Nom du theme

    // Constructeur
    public function __construct(?int $id = null, ?string $nom = null){
        $this->id = $id;
        $this->nom = $nom;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getNom(): ?string{
        return $this->nom;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setNom(?string $nom) : void{
        $this->nom = $nom;
    }
    
}