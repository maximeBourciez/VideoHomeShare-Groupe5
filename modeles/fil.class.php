<?php

// Classe Fil 
class Fil{
    // Attributs
    private ?int $id; // Identifiant
    private ?string $titre; // Titre du fil
    private ?DateTime $dateCreation; // Date de création du fil
    private ?string $description; // Description du fil
    private ?Utilisateur $utilisateur; // Utilisateur qui a créé le fil


    // Constructeur
    public function __construct(?int $id, ?string $titre, ?DateTime $dateCreation, ?string $description, ?Utilisateur $utilisateur = null){
        $this->id = $id;
        $this->titre = $titre;
        $this->dateCreation = $dateCreation;
        $this->description = $description;
        $this->utilisateur = $utilisateur;
    }


    // Encapsulation
    // Getters
    public function getId(): ?int{
        return $this->id;
    }

    public function getTitre(): ?string{
        return $this->titre;
    }

    public function getDateCreation(): ?DateTime{
        return $this->dateCreation;
    }

    public function getDescription(): ?string{
        return $this->description;
    }

    public function getUtilisateur(): ?Utilisateur{
        return $this->utilisateur;
    }


    // Setters
    public function setId(int $id){
        $this->id = $id;
    }

    public function setTitre(string $titre){
        $this->titre = $titre;
    }

    public function setDateCreation(DateTime $dateCreation){
        $this->dateCreation = $dateCreation;
    }

    public function setDescription(string $description){
        $this->description = $description;
    }

    public function setUtilisateur(Utilisateur $utilisateur){
        $this->utilisateur = $utilisateur;
    }

}