<?php

class Question{

    // Attributs
    private ?int $idQuestion;
    private ?string $valeur;
    private ?float $rang;
    private ?string $urlImage;
    private ?int $idQuizz;

    // Constructeur
    public function __construct(?int $idQuestion = null, ?string $valeur = null, ?int $rang = null, ?string $urlImage = null, ?int $idQuizz = null) {
        $this->idQuestion = $idQuestion;
        $this->valeur = $valeur;
        $this->rang = $rang;
        $this->urlImage = $urlImage;
        $this->idQuizz = $idQuizz;
    }

    // Encapsulation

    // Getters

    /**
     * Get the value of idQuestion
     * @return int
     */
    public function getIdQuestion(): ?int
    {
        return $this->idQuestion;
    }

    /**
     * Get the value of valeur
     * @return string
     */
    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    /**
     * Get the value of rang
     * @return float
     */
    public function getRang(): ?float
    {
        return $this->rang;
    }

    /**
     * Get the value of urlImage
     * @return string
     */
    public function getUrlImage(): ?string
    {
        return $this->urlImage;
    }

    /**
     * Get the value of idQuizz
     * @return int
     */
    public function getIdQuizz(): ?int
    {
        return $this->idQuizz;
    }

    // Setters

    /**
     * Set the value of idQuestion
     * @param int $idQuestion
     * @return void
     */
    public function setIdQuestion(?int $idQuestion): void
    {
        $this->idQuestion = $idQuestion;
    }

    /**
     * Set the value of valeur
     * @param string $valeur
     * @return void
     */
    public function setValeur(?string $valeur): void
    {
        $this->valeur = $valeur;
    }

    /**
     * Set the value of rang
     * @param float $rang
     * @return void
     */
    public function setRang(?float $rang): void
    {
        $this->rang = $rang;
    }

    /**
     * Set the value of urlImage
     * @param string $urlImage
     * @return void
     */
    public function setUrlImage(?string $urlImage): void
    {
        $this->urlImage = $urlImage;
    }

    /**
     * Set the value of idQuizz
     * @param int $idQuizz
     * @return void
     */
    public function setIdQuizz(?int $idQuizz): void
    {
        $this->idQuizz = $idQuizz;
    }

}