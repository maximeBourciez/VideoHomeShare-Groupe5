<?php

enum Raison {
    case ContenuInapproprie;
    case Spam;
    case ContenuTrompant;
    case DiscriminationHarcelement;
    case Autre;
}

class Signalement{
    // Attributs 
    private ?int $id; //Identifiant du signalement
    private ?Raison $raison; //Raison du signalement

    // Constructeur
    public function __construct(?int $id = null, ?Raison $raison = null){
        $this->id = $id;
        $this->raison = $raison;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getRaison(): ?Raison{
        return $this->raison;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setRaison(?Raison $raison) : void{
        $this->raison = $raison;
    }
    
}