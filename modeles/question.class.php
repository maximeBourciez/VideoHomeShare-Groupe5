<?php

class Question{

    // Attributs
    private ?int $id;
    private ?string $valeur;
    private ?string $urlImage;

    // Constructeur
    public function __construct(?int $id = null, ?string $valeur = null, ?string $urlImage = null) {
        $this->id = $id;
        $this->valeur = $valeur;
        $this->urlImage = $urlImage;
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
     * Get the value of valeur
     * @return string
     */
    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    /**
     * Get the value of urlImage
     * @return string
     */
    public function getUrlImage(): ?string
    {
        return $this->urlImage;
    }

    // Setters

    /**
     * Set the value of id
     * @param int $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
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
     * Set the value of urlImage
     * @param string $urlImage
     * @return void
     */
    public function setUrlImage(?string $urlImage): void
    {
        $this->urlImage = $urlImage;
    }

    // Méthodes

}