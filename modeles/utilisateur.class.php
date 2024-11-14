<?php

class Utilisateur{
    // Attributs
    private ?string $id;
    private ?string $pseudo;
    private ?string $mail;
    private ?string $mdp;
    private ?string $role;
    private ?string $urlImagePofil;
    private ?string $urlImageBaniere;
    
    // Constructeur
    function __construct(?string $id, ?string $pseudo, ?string $mail, ?string $mdp, ?string $role, ?string $urlImagePofil, ?string $urlImageBaniere){
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->mail = $mail;
        $this->mdp = $mdp;
        $this->role = $role;
        $this->urlImagePofil = $urlImagePofil;
        $this->urlImageBanière = $urlImageBaniere;
    }

    // Encapsulation
    
    
    // Getters

    /**
     * Get the value of id
     * @return string
     */ 
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Get the value of pseudo
     * @return string
     */
    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    /**
     * Get the value of mail
     * @return string
     */
    public function getMail(): ?string
    {
        return $this->mail;
    }

    /**
     * Get the value of mdp
     * @return string
     */
    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    /**
     * Get the value of role
     * @return string
     */
    public function getRole(): ?string
    {
        return $this->role;
    }

    /**
     * Get the value of urlImagePofil
     * @return string
     */
    public function getUrlImageProfil(): ?string
    {
        return $this->urlImagePofil;
    }

    /**
     * Get the value of urlImageBanière
     * @return string
     */
    public function getUrlImageBaniere(): ?string
    {
        return $this->urlImageBanière;
    }

    // Setters

    /**
     * Set the value of id
     * @return void
     */ 
    public function setId(?string $id = null): void
    {
        $this->id = $id;
    }

    /**
     * Set the value of pseudo
     * @return void
     */
    public function setPseudo(?string $pseudo = null): void
    {
        $this->pseudo = $pseudo;
    }

    /**
     * Set the value of mail
     * @return void
     */
    public function setMail(?string $mail = null): void
    {
        $this->mail = $mail;
    }

    /**
     * Set the value of mdp
     * @return void
     */
    public function setMdp(?string $mdp = null): void
    {
        $this->mdp = $mdp;
    }

    /**
     * Set the value of role
     * @return void
     */
    public function setRole(?string $role = null): void
    {
        $this->role = $role;
    }

    /**
     * Set the value of urlImagePofil
     * @return void
     */
    public function setUrlImagePofil(?string $urlImagePofil = null): void
    {
        $this->urlImagePofil = $urlImagePofil;
    }

    /**
     * Set the value of urlImageBanière
     * @return void
     */
    public function setUrlImageBanière(?string $urlImageBanière = null): void
    {
        $this->urlImageBanière = $urlImageBanière;
    }

    // Méthodes 

}