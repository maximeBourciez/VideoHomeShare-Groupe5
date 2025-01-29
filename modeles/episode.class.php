<?php

/**
 * @brief Classe représentant un épisode de série
 * 
 * Cette classe définit la structure et les méthodes pour gérer
 * les informations d'un épisode de série TV
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Episode
{
    /** @var int|null Identifiant de l'épisode */
    private ?int $id;

    /** @var string Titre de l'épisode */
    private string $titre;

    /** @var string Description de l'épisode */
    private string $description;

    /** @var int Numéro de l'épisode dans la saison */
    private int $numero;

    /** @var DateTime Date de diffusion de l'épisode */
    private DateTime $dateDiffusion;

    /** @var string|null URL de l'image de l'épisode */
    private ?string $image;

    /**
     * @brief Constructeur de la classe Episode
     * 
     * @param int|null $id Identifiant de l'épisode
     * @param string $titre Titre de l'épisode
     * @param string $description Description de l'épisode
     * @param int $numero Numéro de l'épisode
     * @param DateTime $dateDiffusion Date de diffusion
     * @param string|null $image URL de l'image
     */
    public function __construct(
        ?int $id,
        string $titre,
        string $description,
        int $numero,
        DateTime $dateDiffusion,
        ?string $image
    ) {
        $this->id = $id;
        $this->titre = $titre;
        $this->description = $description;
        $this->numero = $numero;
        $this->dateDiffusion = $dateDiffusion;
        $this->image = $image;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitre(): string
    {
        return $this->titre;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /**
     * @return DateTime
     */
    public function getDateDiffusion(): DateTime
    {
        return $this->dateDiffusion;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }
}
