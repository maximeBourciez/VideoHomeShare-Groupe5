<?php

class Reponse{
    // Attributs 
    private ?int $id; //Identifiant de la reponse
    private ?string $valeur; //Valeur de la reponse
    private ?string $rang; //Rang de la reponse
    private ?bool $estVraie; //Indicateur de vérité d'une réponse

    // Constructeur
    public function __construct(?int $id = null, ?string $valeur = null, ?string $rang = null, ?bool $estVraie = null){
        $this->id = $id;
        $this->valeur = $valeur;
        $this->rang = $rang;
        $this->estVraie = $estVraie;
    }
    
    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }
    public function getValeur(): ?string{
        return $this->valeur;
    }
    public function getRang(): ?string{
        return $this->rang;
    }
    public function getVerite(): ?bool{
        return $this->estVraie;
    }

    // Setters
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    public function setValeur(?string $valeur) : void{
        $this->valeur = $valeur;
    }
    public function setRang(?string $rang) : void{
        $this->rang = $rang;
    }
    public function setVerite(?bool $estVraie) : void{
        $this->estVraie = $estVraie;
    }

}