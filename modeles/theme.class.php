<?php

/**
 * @brief Classe représentant un thème dans l'application
 * 
 * Cette classe définit la structure d'un thème (genre de film, catégorie, etc.)
 * avec ses propriétés de base et méthodes d'accès. Elle permet de catégoriser
 * les différents contenus multimédias.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Theme {
    /** 
     * @var int|null $idTheme Identifiant unique du thème
     * Clé primaire dans la base de données
     */
    private ?int $idTheme;

    /** 
     * @var string|null $nom Nom du thème
     * Libellé descriptif du thème (ex: "Action", "Comédie", etc.)
     */
    private ?string $nom;

    /**
     * @brief Constructeur de la classe Theme
     * 
     * Initialise un nouveau thème avec un identifiant et un nom optionnels.
     * Les valeurs peuvent être nulles lors de la création d'un nouveau thème
     * non encore persisté en base de données.
     * 
     * @param int|null $idTheme Identifiant unique du thème
     * @param string|null $nom Nom du thème
     */
    public function __construct(?int $idTheme = null, ?string $nom = null) {
        $this->idTheme = $idTheme;
        $this->nom = $nom;
    }

    /**
     * @brief Récupère l'identifiant du thème
     * 
     * @return int|null L'identifiant unique du thème ou null si non défini
     */
    public function getId(): ?int {
        return $this->idTheme;
    }

    /**
     * @brief Récupère le nom du thème
     * 
     * @return string|null Le nom du thème ou null si non défini
     */
    public function getNom(): ?string {
        return $this->nom;
    }

    /**
     * @brief Définit l'identifiant du thème
     * 
     * @param int|null $idTheme Nouvel identifiant unique du thème
     * @return void
     */
    public function setId(?int $idTheme): void {
        $this->idTheme = $idTheme;
    }

    /**
     * @brief Définit le nom du thème
     * 
     * @param string|null $nom Nouveau nom du thème
     * @return void
     */
    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }
}