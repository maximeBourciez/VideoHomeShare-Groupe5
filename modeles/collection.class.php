<?php

/**
 * @brief Classe représentant une collection de films
 * 
 * Cette classe définit la structure d'une collection de films
 * avec ses propriétés et méthodes d'accès
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Collection
{
    /** @var int|null Identifiant unique de la collection */
    private ?int $id;

    /** @var string|null Titre de la collection */
    private ?string $titre;

    /** @var DateTime|null Date de création/modification de la collection */
    private ?DateTime $date;

    /** @var string|null Description détaillée de la collection */
    private ?string $description;

    /** @var string|null Lien vers l'affiche de la collection */
    private ?string $lienAffiche;

    /** @var int|null Nombre de films dans la collection */
    private ?int $nombreFilms;

    /**
     * @brief Constructeur de la classe Collection
     * 
     * @param int|null $id Identifiant de la collection
     * @param string|null $titre Titre de la collection
     * @param DateTime|null $date Date de la collection
     * @param string|null $description Description de la collection
     * @param string|null $lienAffiche Lien vers l'affiche
     * @param int|null $nombreFilms Nombre de films dans la collection
     */
    public function __construct(?int $id = null, ?string $titre = null, ?DateTime $date = null, ?string $description = null, ?string $lienAffiche = null, ?int $nombreFilms = null)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->date = $date;
        $this->description = $description;
        $this->lienAffiche = $lienAffiche;
        $this->nombreFilms = $nombreFilms;
    }

    /**
     * @brief Récupère l'identifiant de la collection
     * @return int|null L'identifiant de la collection
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @brief Définit l'identifiant de la collection
     * @param int|null $id Le nouvel identifiant
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @brief Récupère le titre de la collection
     * @return string|null Le titre de la collection
     */
    public function gettitre(): ?string
    {
        return $this->titre;
    }
    /**
     * @brief Définit le titre de la collection
     * @param string|null $titre Le nouveau titre
     */
    public function settitre(?string $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * @brief Récupère la date de la collection
     * @return DateTime|null La date de la collection
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }
    /**
     * @brief Définit la date de la collection
     * @param DateTime|null $date La nouvelle date
     */
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
    /**
     * @brief Récupère la description de la collection
     * @return string|null La description de la collection
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
    /**
     * @brief Définit la description de la collection
     * @param string|null $description La nouvelle description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
    /**
     * @brief Récupère le lien de l'affiche de la collection
     * @return string|null Le lien de l'affiche
     */
    public function getLienAffiche(): ?string
    {
        return $this->lienAffiche;
    }
    /**
     * @brief Définit le lien de l'affiche de la collection
     * @param string|null $lienAffiche Le nouveau lien de l'affiche
     */
    public function setLienAffiche(?string $lienAffiche): void
    {
        $this->lienAffiche = $lienAffiche;
    }
    /**
     * @brief Récupère le nombre de films dans la collection
     * @return int|null Le nombre de films
     */
    public function getNombreFilms(): ?int
    {
        return $this->nombreFilms;
    }
    /**
     * @brief Définit le nombre de films dans la collection
     * @param int|null $nombreFilms Le nouveau nombre de films
     */
    public function setNombreFilms(?int $nombreFilms): void
    {
        $this->nombreFilms = $nombreFilms;
    }

    /** 
     * @brief Renvoie la classe de l'objet sous forme de chaîne de caractères
     * @return string La classe de l'objet
     */
    public function getClass(): string
    {
        return "Collection";
    }
}
