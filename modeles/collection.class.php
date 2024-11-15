<?php

enum TypeCollection{
    case Saga;
    case Serie;
}

class Collection{
    // Attributs 
    private ?int $id; //Identifiant de la collection
    private ?TypeCollection $type; //Type de collection
    private ?string $nom; //Nom de la collection

    // Constructeur
    public function __construct(?int $id = null, ?TypeCollection $type = null, ?string $nom = null){
        $this->id = $id;
        $this->type = $type;
        $this->nom = $nom;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getType(): ?TypeCollection{
        return $this->type;
    }
    public function getNom(): ?string{
        return $this->nom;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setType(?TypeCollection $type) : void{
        $this->type = $type;
    }
    public function setNom(?string $nom) : void{
        $this->nom = $nom;
    }

}