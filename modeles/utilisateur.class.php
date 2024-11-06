<?php

class Utilisateur{
    // Attributs
    private ?int $id;
    private ?string $pseudo;
    private ?string $mail;
    private ?string $mdp;
    private ?string $role;
    private ?string $urlImagePofil;
    private ?string $urlImageBanière;
    
    // Constructeur
    function __construct(?int $id, ?string $pseudo, ?string $mail, ?string $mdp, ?string $role, ?string $urlImagePofil, ?string $urlImageBanière){
        $this->id = $id;
        $this->pseudo = $pseudo;
        $this->mail = $mail;
        $this->mdp = $mdp;
        $this->role = $role;
        $this->urlImagePofil = $urlImagePofil;
        $this->urlImageBanière = $urlImageBanière;
    }

    // Encapsulation
    
    
    // Getters

    /**
     * Get the value of id
     * @return int
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of pseudo
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Get the value of mail
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Get the value of mdp
     * @return string
     */
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * Get the value of role
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Get the value of urlImagePofil
     * @return string
     */
    public function getUrlImagePofil()
    {
        return $this->urlImagePofil;
    }

    /**
     * Get the value of urlImageBanière
     * @return string
     */
    public function getUrlImageBanière()
    {
        return $this->urlImageBanière;
    }

    // Setters

    /**
     * Set the value of id
     * @return void
     */ 
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set the value of pseudo
     * @return void
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;
    }

    /**
     * Set the value of mail
     * @return void
     */

    // Méthodes 

}