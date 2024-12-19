<?php

/**
 * @brief Classe représentant une personnalité du cinéma
 * 
 * Cette classe définit la structure d'une personnalité (acteur, réalisateur, etc.)
 * avec ses informations de base comme le nom, prénom, rôle et image.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Personnalite {
    /** @var int|null Identifiant unique de la personnalité */
    private ?int $id;

    /** @var string|null Nom de famille de la personnalité */
    private ?string $nom;

    /** @var string|null Prénom de la personnalité */
    private ?string $prenom;

    /** @var string|null URL de l'image de la personnalité */
    private ?string $urlImage;

    /** @var string|null Rôle dans la production (acteur, réalisateur, etc.) */
    private ?string $role;

    /**
     * @brief Constructeur de la classe Personnalite
     * 
     * @param int|null $id Identifiant unique
     * @param string|null $nom Nom de famille
     * @param string|null $prenom Prénom
     * @param string|null $urlImage URL de l'image
     * @param string|null $role Rôle dans la production
     */
    function __construct(
        ?int $id = null, 
        ?string $nom = null, 
        ?string $prenom = null, 
        ?string $urlImage = null, 
        ?string $role = null
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->urlImage = $urlImage;
        $this->role = $role;
    }

    /**
     * @brief Récupère l'identifiant de la personnalité
     * @return int|null L'identifiant
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @brief Définit l'identifiant de la personnalité
     * @param int $id Le nouvel identifiant
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @brief Récupère le nom de la personnalité
     * @return string|null Le nom de famille
     */
    public function getNom(): ?string {
        return $this->nom;
    }

    /**
     * @brief Définit le nom de la personnalité
     * @param string $nom Le nouveau nom
     */
    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    /**
     * @brief Récupère le prénom de la personnalité
     * @return string|null Le prénom
     */
    public function getPrenom(): ?string {
        return $this->prenom;
    }

    /**
     * @brief Définit le prénom de la personnalité
     * @param string $prenom Le nouveau prénom
     */
    public function setPrenom(string $prenom): void {
        $this->prenom = $prenom;
    }

    /**
     * @brief Récupère l'URL de l'image
     * @return string|null L'URL de l'image
     */
    public function getUrlImage(): ?string {
        return $this->urlImage;
    }

    /**
     * @brief Définit l'URL de l'image
     * @param string $urlImage La nouvelle URL
     */
    public function setUrlImage(string $urlImage): void {
        $this->urlImage = $urlImage;
    }

    /**
     * @brief Récupère le rôle de la personnalité
     * @return string|null Le rôle (acteur, réalisateur, etc.)
     */
    public function getRole(): ?string {
        return $this->role;
    }

    /**
     * @brief Définit le rôle de la personnalité
     * @param string $role Le nouveau rôle
     */
    public function setRole(string $role): void {
        $this->role = $role;
    }
}