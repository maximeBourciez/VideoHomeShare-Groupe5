<?php

enum Difficulte {
    case Facile;
    case Moyen;
    case Difficile;
    case Expert;
}

class Quizz {

    // Attributs
    private ?int $id;
    private ?string $titre;
    private ?string $description;
    private ?Difficulte $difficulte;
    private ?string $date;

    // Constructeur
    function __construct(?int $id = null, ?string $titre = null, ?string $description = null, ?Difficulte $difficulte = null, ?string $date = null){
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->difficulte = $difficulte;
        $this->date = $date;
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
     * @return Difficulte
     */
    public function getDifficulte(): ?Difficulte
    {
        return $this->difficulte;
    }

    /**
     * Get the value of date
     * @return string
     */
    public function getDate(): ?string
    {
        return $this->date;
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
     * @param Difficulte $difficulte
     * @return void
     */
    public function setDifficulte(Difficulte $difficulte): void
    {
        $this->difficulte = $difficulte;
    }

    /**
     * Set the value of date
     * @param string $date
     * @return void
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    // Méthodes

}