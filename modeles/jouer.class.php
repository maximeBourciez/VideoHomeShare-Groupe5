<?php

class Jouer {

    // Attributs
    private ?string $idUtilisateur;
    private ?int $idQuizz;
    private ?int $score;
    

    // Constructeur
    function __construct(?string $idUtilisateur = null, ?int $idQuizz = null, ?int $score = null){
        $this->idUtilisateur = $idUtilisateur;
        $this->idQuizz = $idQuizz;
        $this->score = $score;
    }

    // Encapsulation

    // Getters

    /**
     * Get the value of idUtilisateur
     * @return string
     */
    public function getIdUtilisateur(): ?string
    {
        return $this->idUtilisateur;
    }

    /**
     * Get the value of idQuizz
     * @return int
     */
    public function getIdQuizz(): ?int
    {
        return $this->idQuizz;
    }

    /**
     * Get the value of score
     * @return int
     */
    public function getScore(): ?int
    {
        return $this->score;
    }

    // Setters

    /**
     * Set the value of idUtilisateur
     * @param string $idUtilisateur
     * @return void
     */
    public function setIdUtilisateur(string $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * Set the value of idQuizz
     * @param int $idQuizz
     * @return void
     */
    public function setIdQuizz(int $idQuizz): void
    {
        $this->idQuizz = $idQuizz;
    }

    /**
     * Set the value of score
     * @param int $score
     * @return void
     */
    public function setScore(int $score): void
    {
        $this->score = $score;
    }

}