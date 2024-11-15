<?php

class Signalement{
    // Attributs 
    private ?int $id; //Identifiant du signalement
    private ?string $raison; //Raison du signalement

    // Constructeur
    public function __construct(?int $id = null, ?string $raison = null){
        $this->id = $id;
        $this->raison = $raison;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getRaison(): ?string{
        return $this->raison;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setRaison(?string $raison) : void{
        $this->raison = $raison;
    }
    
}