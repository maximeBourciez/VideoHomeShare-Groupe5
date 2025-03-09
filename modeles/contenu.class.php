<?php 

/**
 * @brief Classe représentant un contenu multimédia
 * 
 * Cette classe définit la structure d'un contenu (film, série, etc.)
 * avec toutes ses propriétés et méthodes d'accès. Elle gère les informations
 * comme le titre, la date, les descriptions et les liens vers les affiches.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Contenu {   
    /** @var int|null Identifiant unique du contenu */
    private ?int $id;

    /** @var string|null Titre du contenu */
    private ?string $titre;

    /** @var DateTime|null Date de sortie du contenu */
    private ?DateTime $date;

    /** @var string|null Description courte du contenu */
    private ?string $description;

    /** @var string|null Description détaillée du contenu */
    private ?string $DescriptionLongue;

    /** @var string|null Lien vers l'affiche en taille normale */
    private ?string $lienAffiche;

    /** @var string|null Type de contenu (film, série, etc.) */
    private ?string $type;

    /** @var string|null Durée du contenu au format "HHhMM" */
    private ?string $duree;

    /** @var int|null Identifiant TMDB du contenu */
    private ?int $tmdbId;

    /** @var string|null Lien vers l'affiche en taille réduite */
    private ?string $lienAfficheReduite;
  
    /**
     * @brief Constructeur de la classe Contenu
     * 
     * @param int|null $id Identifiant unique
     * @param string|null $titre Titre du contenu
     * @param DateTime|null $date Date de sortie
     * @param string|null $description Description courte
     * @param string|null $DescriptionLongue Description détaillée
     * @param string|null $lienAffiche Lien vers l'affiche
     * @param string|null $duree Durée du contenu
     * @param string|null $type Type de contenu
     * @param string|null $lienAfficheReduite Lien vers l'affiche réduite
     */
    public function __construct(
        ?int $id = null, 
        ?string $titre = null, 
        ?DateTime $date = null, 
        ?string $description = null, 
        ?string $DescriptionLongue = null, 
        ?string $lienAffiche = null, 
        ?string $duree = null, 
        ?string $type = null, 
        ?string $lienAfficheReduite = null
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->date = $date;
        $this->description = $description;
        $this->DescriptionLongue = $DescriptionLongue;
        $this->lienAffiche = $lienAffiche;
        $this->duree = $duree;
        $this->type = $type;
        $this->tmdbId = null;
        $this->lienAfficheReduite = $lienAfficheReduite;
    }
  
    /**
     * @brief Récupère l'identifiant du contenu
     * @return int|null L'identifiant
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @brief Définit l'identifiant du contenu
     * @param int|null $id Le nouvel identifiant
     */
    public function setId(?int $id): void {
        $this->id = $id;
    }

    /**
     * @brief Récupère le titre du contenu
     * @return string|null Le titre
     */
    public function getTitre(): ?string {
        return $this->titre;
    }

    /**
     * @brief Définit le titre du contenu
     * @param string|null $titre Le nouveau titre
     */
    public function setTitre(?string $titre): void {
        $this->titre = $titre;
    }

    /**
     * @brief Récupère la date de sortie
     * @return DateTime|null La date
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @brief Définit la date de sortie
     * @param DateTime|null $date La nouvelle date
     */
    public function setDate(?DateTime $date): void {
        $this->date = $date;
    }

    /**
     * @brief Récupère la description courte
     * @return string|null La description
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @brief Définit la description courte
     * @param string|null $description La nouvelle description
     */
    public function setDescription(?string $description): void {
        $this->description = $description;
    }

    /**
     * @brief Récupère le lien vers l'affiche
     * @return string|null Le lien
     */
    public function getLienAffiche(): ?string {
        return $this->lienAffiche;
    }

    /**
     * @brief Définit le lien vers l'affiche
     * @param string|null $lienAffiche Le nouveau lien
     */
    public function setLienAffiche(?string $lienAffiche): void {
        $this->lienAffiche = $lienAffiche;
    }

    /**
     * @brief Récupère la durée du contenu
     * @return string|null La durée au format "HHhMM"
     */
    public function getDuree(): ?string {
        return $this->duree;
    }

    /**
     * @brief Définit la durée du contenu
     * @param string|null $duree La nouvelle durée
     */
    public function setDuree(?string $duree): void {
        $this->duree = $duree;
    }

    /**
     * @brief Récupère le type de contenu
     * @return string|null Le type
     */
    public function getType(): ?string {
        return $this->type;
    }

    /**
     * @brief Définit le type de contenu
     * @param string|null $type Le nouveau type
     */
    public function setType(?string $type): void {
        $this->type = $type;
    }

    /**
     * @brief Récupère la description détaillée
     * @return string|null La description longue
     */
    public function getDescriptionLongue(): ?string {
        return $this->DescriptionLongue;
    }

    /**
     * @brief Définit la description détaillée
     * @param string|null $DescriptionLongue La nouvelle description
     */
    public function setDescriptionLongue(?string $DescriptionLongue): void {
        $this->DescriptionLongue = $DescriptionLongue;
    }

    /**
     * @brief Récupère l'identifiant TMDB
     * @return int|null L'identifiant TMDB
     */
    public function getTmdbId(): ?int {
        return $this->tmdbId;
    }

    /**
     * @brief Définit l'identifiant TMDB
     * @param int|null $tmdbId Le nouvel identifiant TMDB
     * @return self
     */
    public function setTmdbId(?int $tmdbId): self {
        $this->tmdbId = $tmdbId;
        return $this;
    }

    /**
     * @brief Récupère le lien vers l'affiche réduite
     * @return string|null Le lien
     */
    public function getLienAfficheReduite(): ?string {
        return $this->lienAfficheReduite;
    }

    /**
     * @brief Définit le lien vers l'affiche réduite
     * @param string|null $lienAfficheReduite Le nouveau lien
     */
    public function setLienAfficheReduite(?string $lienAfficheReduite): void {
        $this->lienAfficheReduite = $lienAfficheReduite;
    }

    /**
     * @brief Renvoie la classe de l'objet sous forme de chaîne de caractères
     * @return string La classe de l'objet
     */
    public function getClass(): string {
        return "Contenu";
    }
}