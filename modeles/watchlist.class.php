<?php

class Watchlist {
    private int $id;
    private string $nom;
    private string $description;
    private bool $estPublique;
    private DateTime $date;
    private string $idUtilisateur;

    public function __construct(
        int $id,
        string $nom,
        string $description,
        bool $estPublique,
        DateTime $date,
        string $idUtilisateur
    ) {
        $this->id = $id;
        $this->nom = $nom;
        $this->description = $description;
        $this->estPublique = $estPublique;
        $this->date = $date;
        $this->idUtilisateur = $idUtilisateur;
    }

    // Getters

    public function getId(): int {
        return $this->id;
    }

    public function getNom(): string {
        return $this->nom;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function isEstPublique(): bool {
        return $this->estPublique;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function getIdUtilisateur(): string {
        return $this->idUtilisateur;
    }

    // Setters

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function setNom(string $nom): void {
        $this->nom = $nom;
    }

    public function setDescription(string $description): void {
        $this->description = $description;
    }

    public function setEstPublique(bool $estPublique): void {
        $this->estPublique = $estPublique;
    }

    public function setDate(DateTime $date): void {
        $this->date = $date;
    }

    public function setIdUtilisateur(string $idUtilisateur): void {
        $this->idUtilisateur = $idUtilisateur;
    }
}
