<?php

class Collection{
    // Attributs 
    private ?int $id; //Identifiant de la collection
    private ?string $type; //Type de collection
    private ?string $nom; //Nom de la collection

    // Constructeur
    public function __construct(?int $id = null, ?string $type = null, ?string $nom = null){
        $this->id = $id;
        $this->type = $type;
        $this->nom = $nom;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getType(): ?string{
        return $this->type;
    }
    public function getNom(): ?string{
        return $this->nom;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setType(?string $type) : void{
        $this->type = $type;
    }
    public function setNom(?string $nom) : void{
        $this->nom = $nom;
    }

}