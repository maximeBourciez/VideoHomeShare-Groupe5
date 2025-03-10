<?php
/**
 * @brief Classe représentant une watchlist
 * @details Gère les données d'une watchlist et ses contenus associés
 * @author VotreNom
 * @version 1.0
 */
class Watchlist {
    private int $id;
    private string $nom;
    private string $description;
    private bool $estPublique;
    private DateTime $date;
    private string $idUtilisateur;
    private array $contenus = [];
    private array $partages = [];

    /**
     * @brief Constructeur de la classe Watchlist
     * @param int $id Identifiant unique de la watchlist
     * @param string $nom Nom de la watchlist
     * @param string $description Description de la watchlist
     * @param bool $estPublique Statut public/privé de la watchlist
     * @param DateTime $date Date de création
     * @param string $idUtilisateur Identifiant de l'utilisateur propriétaire
     * @param array $contenus Liste des contenus de la watchlist
     * @param array $partages Liste des utilisateurs avec qui la watchlist est partagée
     */
    public function __construct(
        int $id,
        string $nom,
        string $description,
        bool $estPublique,
        DateTime $date,
        string $idUtilisateur,
        array $contenus = [],
        array $partages = []
    ) {
        $this->id = $id;
        $this->nom = htmlspecialchars($nom);
        $this->description = htmlspecialchars($description);
        $this->estPublique = $estPublique;
        $this->date = $date;
        $this->idUtilisateur = htmlspecialchars($idUtilisateur);
        $this->contenus = $contenus;
        $this->partages = $partages;
    }

    /**
     * @brief Récupère l'ID de la watchlist
     * @return int L'identifiant de la watchlist
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @brief Récupère le nom de la watchlist
     * @return string Le nom de la watchlist
     */
    public function getNom(): string {
        return $this->nom;
    }

    /**
     * @brief Récupère la description de la watchlist
     * @return string La description de la watchlist
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @brief Vérifie si la watchlist est publique
     * @return bool True si la watchlist est publique, false sinon
     */
    public function isEstPublique(): bool {
        return $this->estPublique;
    }

    /**
     * @brief Récupère la date de création
     * @return DateTime La date de création
     */
    public function getDate(): DateTime {
        return $this->date;
    }

    /**
     * @brief Récupère l'ID de l'utilisateur
     * @return string L'identifiant de l'utilisateur
     */
    public function getIdUtilisateur(): string {
        return $this->idUtilisateur;
    }

    /**
     * @brief Récupère les contenus de la watchlist
     * @return array La liste des contenus
     */
    public function getContenus(): array {
        return $this->contenus;
    }

    // Setters avec sécurisation des entrées

    /**
     * @brief Définit l'ID de la watchlist
     * @param int $id Le nouvel identifiant
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * @brief Définit le nom de la watchlist
     * @param string $nom Le nouveau nom
     */
    public function setNom(string $nom): void {
        $this->nom = htmlspecialchars($nom);
    }

    /**
     * @brief Définit la description de la watchlist
     * @param string $description La nouvelle description
     */
    public function setDescription(string $description): void {
        $this->description = htmlspecialchars($description);
    }

    /**
     * @brief Définit si la watchlist est publique
     * @param bool $estPublique Le nouveau statut
     */
    public function setEstPublique(bool $estPublique): void {
        $this->estPublique = $estPublique;
    }

    /**
     * @brief Définit la date de création
     * @param DateTime $date La nouvelle date
     */
    public function setDate(DateTime $date): void {
        $this->date = $date;
    }

    /**
     * @brief Définit l'ID de l'utilisateur
     * @param string $idUtilisateur Le nouvel identifiant utilisateur
     */
    public function setIdUtilisateur(string $idUtilisateur): void {
        $this->idUtilisateur = htmlspecialchars($idUtilisateur);
    }

    /**
     * @brief Définit les contenus de la watchlist
     * @param array $contenus La nouvelle liste de contenus
     */
    public function setContenus(array $contenus): void {
        $this->contenus = $contenus;
    }

    /**
     * @brief Définit les partages de la watchlist
     * @param array $partages La nouvelle liste de partages
     */
    public function setPartages(array $partages): void {
        $this->partages = $partages;
    }

    /**
     * @brief Récupère les partages de la watchlist
     * @return array La liste des partages
     */
    public function getPartages(): array {
        return $this->partages;
    }
}