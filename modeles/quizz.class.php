<?php

class Quizz {

    // Attributs
    private ?int $id;
    private ?string $titre;
    private ?string $description;
    private ?int $difficulte; //Prend une valeur entière entre 1 à 4
    private ?string $dateC;
    private ?string $idUtilisateur;

    // Constructeur
    function __construct(?int $id = null, ?string $titre = null, ?string $description = null, ?int $difficulte = null, ?string $dateC = null, ?string $idUtilisateur = null){
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
     * @return int
     */
    public function getDifficulte(): ?int
    {
        return $this->difficulte;
    }

    /**
     * Get the value of date
     * @return string
     */
    public function getDate(): ?string
    {
        return $this->dateC;
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
     * @param int $difficulte
     * @return void
     */
    public function setDifficulte(int $difficulte): void
    {
        $this->difficulte = $difficulte;
    }

    /**
     * Set the value of date
     * @param string $date
     * @return void
     */
    public function setDate(string $dateC): void
    {
        $this->date = $dateC;
    }

    /**
     * Set the value of idUtilisateur
     * @param string $idUtilisateur
     * @return void
     */
    public function setIdUtilisateur(string $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

}