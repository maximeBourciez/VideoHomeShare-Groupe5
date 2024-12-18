<?php

/**
 * Classe représentant un thème dans l'application.
 * 
 * Cette classe permet de gérer les thèmes (films, séries, etc.) avec un identifiant unique et un nom.
 */
class Theme {
    /**
     * @var int|null $idTheme Identifiant unique du thème.
     */
    private ?int $idTheme;

    /**
     * @var string|null $nom Nom du thème.
     */
    private ?string $nom;

    /**
     * Constructeur de la classe Theme.
     * 
     * @param int|null $idTheme   Identifiant unique du thème (optionnel).
     * @param string|null $nom Nom du thème (optionnel).
     */
    public function __construct(?int $idTheme = null, ?string $nom = null) {
        $this->idTheme = $idTheme;
        $this->nom = $nom;
    }

    /**
     * Obtient l'identifiant unique du thème.
     * 
     * @return int|null Identifiant unique du thème.
     */
    public function getId(): ?int {
        return $this->idTheme;
    }

    /**
     * Obtient le nom du thème.
     * 
     * @return string|null Nom du thème.
     */
    public function getNom(): ?string {
        return $this->nom;
    }

    /**
     * Définit l'identifiant unique du thème.
     * 
     * @param int|null $idTheme Nouvel identifiant unique du thème.
     * @return void
     */
    public function setId(?int $idTheme): void {
        $this->idTheme = $idTheme;
    }

    /**
     * Définit le nom du thème.
     * 
     * @param string|null $nom Nouveau nom du thème.
     * @return void
     */
    public function setNom(?string $nom): void {
        $this->nom = $nom;
    }
}