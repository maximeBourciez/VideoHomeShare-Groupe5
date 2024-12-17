<?php

class Personnalite{
    // Attributs
    private ?int $id;
    private ?string $nom;
    private ?string $prenom;
    private ?string $urlImage;
    private ?string $role;

    // Constructeur
    function __construct(?int $id = null, ?string $nom = null, ?string $prenom = null, ?string $urlImage = null, ?string $role = null){
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->urlImage = $urlImage;
        $this->role = $role;
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
     * Get the value of nom
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Get the value of prenom
     * @return string
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
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
     * Get the value of role
     * @return string $role
     * @return void
     */
    public function getRole(): ?string
    {
        return $this->role;
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
     * Set the value of nom
     * @param string $nom
     * @return void
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * Set the value of prenom
     * @param string $prenom
     * @return void
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * Set the value of urlImage
     * @param string $urlImage
     * @return void
     */
    public function setUrlImage(string $urlImage): void
    {
        $this->urlImage = $urlImage;
    }

    /**
     * Set the value of role
     * @param string $role
     * @return void
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    // méthodes

}