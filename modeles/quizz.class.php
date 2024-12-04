<?php

class Quizz {

    // Attributs
    private ?int $id;
    private ?string $titre;
    private ?string $description;
    private ?string $difficulte;
    private ?string $dateC;
    private ?string $idUtilisateur;

    // Constructeur
    function __construct(?int $id = null, ?string $titre = null, ?string $description = null, ?string $difficulte = null, ?string $dateC = null, ?string $idUtilisateur = null){
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->difficulte = $difficulte;
        $this->dateC = $dateC;
        $this->idUtilisateur = $idUtilisateur;
    }

    // Encapsulation

    // Getters

    /**
     * Get the value of id
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of titre
     * @return string
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Get the value of description
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the value of difficulte
     * @return string
     */
    public function getDifficulte(): ?string
    {
        return $this->difficulte;
    }

    /**
     * Get the value of date
     * @return string
     */
    public function getDateC(): ?string
    {
        return $this->dateC;
    }

    /**
     * Get the value of idUtilisateur
     * @return string
     */
    public function getIdUtilisateur(): ?string
    {
        return $this->idUtilisateur;
    }    

    // Setters

    /**
     * Set the value of id
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Set the value of titre
     * @param string $titre
     * @return void
     */
    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * Set the value of description
     * @param string $description
     * @return void
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * Set the value of difficulte
     * @param string $difficulte
     * @return void
     */
    public function setDifficulte(string $difficulte): void
    {
        $this->difficulte = $difficulte;
    }

    /**
     * Set the value of date
     * @param string $date
     * @return void
     */
    public function setDateC(string $dateC): void
    {
        $this->dateC = $dateC;
    }

    /**
     * Get the value of idUtilisateur
     * @param string $idUtilisateur
     * @return void
     */
    public function setIdUtilisateur(string $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    } 

    // Méthodes

}