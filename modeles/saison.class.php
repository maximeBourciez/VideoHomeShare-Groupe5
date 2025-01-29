<?php

/**
 * @brief Classe représentant une saison d'une série TV
 *
 * Cette classe contient toutes les informations relatives à une saison
 *
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Saison
{
    private int $id;
    private string $nom;
    private string $description;
    private int $numero;
    private int $nombreEpisodes;
    private ?string $lienAffiche;
    private DateTime $datePremiereAir;

    /**
     * @brief Constructeur de la classe Saison
     *
     * @param int $id Identifiant de la saison
     * @param string $nom Nom de la saison
     * @param string $description Description de la saison
     * @param int $numero Numéro de la saison
     * @param int $nombreEpisodes Nombre d'épisodes dans la saison
     * @param string|null $lienAffiche Lien vers l'affiche de la saison
     * @param DateTime $datePremiereAir Date de première diffusion de la saison
     */
    public function __construct(
        int $id,
        string $nom,
        string $description,
        int $numero,
        int $nombreEpisodes,
        ?string $lienAffiche,
        DateTime $datePremiereAir
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->numero = $numero;
        $this->nombreEpisodes = $nombreEpisodes;
        $this->lienAffiche = $lienAffiche;
        $this->datePremiereAir = $datePremiereAir;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getNumero(): int
    {
        return $this->numero;
    }

    /**
     * @param int $numero
     */
    public function setNumero(int $numero): void
    {
        $this->numero = $numero;
    }

    /**
     * @return int
     */
    public function getNombreEpisodes(): int
    {
        return $this->nombreEpisodes;
    }

    /**
     * @param int $nombreEpisodes
     */
    public function setNombreEpisodes(int $nombreEpisodes): void
    {
        $this->nombreEpisodes = $nombreEpisodes;
    }

    /**
     * @return string|null
     */
    public function getLienAffiche(): ?string
    {
        return $this->lienAffiche;
    }

    /**
     * @param string|null $lienAffiche
     */
    public function setLienAffiche(?string $lienAffiche): void
    {
        $this->lienAffiche = $lienAffiche;
    }

    /**
     * @return DateTime
     */
    public function getDatePremiereAir(): DateTime
    {
        return $this->datePremiereAir;
    }

    /**
     * @param DateTime $datePremiereAir
     */
    public function setDatePremiereAir(DateTime $datePremiereAir): void
    {
        $this->datePremiereAir = $datePremiereAir;
    }
}
