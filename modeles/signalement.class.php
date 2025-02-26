<?php

/**
 * @file signalement.class.php
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 * 
 * @date 18/12/2024
 * 
 * @version 2.0
 */


enum RaisonSignalement: string {
    case ContenuInapproprie = 'Contenu inapproprié';
    case Spam = 'Spam';
    case ContenuTrompant = 'Contenu trompant';
    case DiscriminationHarcelement = 'Discrimination ou harcèlement';
    case Autre = 'Autre';

    /**
     * Récupère toutes les valeurs de l'énumération sous forme de tableau de chaînes.
     */
    public static function getAllReasons(): array {
        return array_map(fn(self $reason) => $reason->value, self::cases());
    }

    /**
     * Convertit une chaîne de caractères en instance de RaisonSignalement.
     *
     * @param string $value
     * @return RaisonSignalement|null
     */
    public static function fromString(string $value): ?self {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null; // Retourne null si aucune correspondance trouvée
    }

    /**
     * Convertit l'instance de RaisonSignalement en chaîne de caractères.
     *
     * @return string
     */
    public function toString(): string {
        return $this->value;
    }

    /**
     * Vérifie si la valeur donnée est une raison de signalement valide.
     *
     * @param string $value
     * @return bool
     */
    public static function isValidReason(string $value): bool {
        return in_array($value, self::getAllReasons(), true);
    }
}

class Signalement{
    // Attributs 
    /**
     * @var int|null $id Identifiant du signalement
     */
    private ?int $idSignalement; 
    /**
     * @var RaisonSignalement|null $raison Raison du signalement
     */
    private ?RaisonSignalement $raison; 
    /**
     * @var string|null $idUtilisateur Identifiant de l'utilisateur qui signale
     */
    private ?string $idUtilisateur;
    /**
     * @var int|null $idMessage Identifiant du message signalé
     */
    private ?int $idMessage;

    /**
     * @var bool|null $estAutomatique Indique si le signalement a été généré automatiquement
     */
    private ?bool $estAutomatique;

    /**
     * @var string|null $contenu Contenu du signalement (message profane dans le cas ou le signalement est automatique)
     */
    private ?string $contenu;

    /**
     * Constructeur de message 
     * 
     * @param int|null $id Identifiant du signalement
     * @param RaisonSignalement|null $raison Raison du signalement
     * @param string|null $idUtilisateur Identifiant de l'utilisateur qui signale
     * @param int|null $idMessage Identifiant du message signalé
     * @param bool|null $estAutomatique Indique si le signalement a été généré automatiquement
     * @param string|null $contenu Contenu du signalement
     */
    public function __construct(?int $id = null, ?RaisonSignalement $Raison = null, ?int $idUtilisateur = null, ?int $idMessage = null, ?bool $estAutomatique = null, ?string $contenu = null){
        $this->idSignalement = $id;
        $this->raison = $Raison;
        $this->idUtilisateur = $idMessage;
        $this->idMessage = $idMessage;

        // Ajouter le contenu si le signalement est automatique
        if($estAutomatique){
            $this->contenu = $contenu;
        }
        $this->estAutomatique = $estAutomatique;
    }
    
    // Encapsulation
    // Getters
    /**
     * @brief Getter de l'identifiant du signalement
     * 
     * @return int|null
     */
    public function getId(): ?int{
        return $this->idSignalement;
    }

    /**
     * @brief Getter de la raison du signalement
     * 
     * @return RaisonSignalement|null
     */
    public function getRaison(): ?RaisonSignalement{
        return $this->raison;
    }

    /**
     * @brief Getter de l'identifiant de l'utilisateur
     * 
     * @return string|null
     */
    public function getIdUtilisateur(): ?string{
        return $this->idUtilisateur;
    }

    /**
     * @brief Getter de l'identifiant du message
     * 
     * @return int|null
     */
    public function getIdMessage(): ?int{
        return $this->idMessage;
    }

    /**
     * @brief Getter de l'indicateur de signalement automatique
     * 
     * @return bool|null
     */
    public function getEstAutomatique(): ?bool {
        return $this->estAutomatique;
    }

    /**
     * @brief Getter du contenu du signalement
     * 
     * @return string|null
     */
    public function getContenu(): ?string {
        return $this->contenu;
    }


    // Setters
    /**
     * @brief Setter de l'identifiant du signalement
     * 
     * @param int|null $id Identifiant du signalement
     * 
     * @return void
     */
    public function setId(?int $id) : void{
        $this->idSignalement = $id;
    }

    /**
     * @brief Setter de la raison du signalement
     * 
     * @param RaisonSignalement|null $raison Raison du signalement
     * 
     * @return void
     */
    public function setRaison(?RaisonSignalement $raison) : void{
        $this->raison = $raison;
    }

    /**
     * @brief Setter de l'identifiant de l'utilisateur
     * 
     * @param string|null $idUtilisateur Identifiant de l'utilisateur
     * 
     * @return void
     */
    public function setIdUtilisateur(?string $idUtilisateur) : void{
        $this->idUtilisateur = $idUtilisateur;
    }

    /**
     * @brief Setter de l'identifiant du message
     * 
     * @param int|null $idMessage Identifiant du message
     * 
     * @return void
     */
    public function setIdMessage(?int $idMessage) : void{
        $this->idMessage = $idMessage;
    }

    /**
     * @brief Setter de l'indicateur de signalement automatique
     * 
     * @param bool|null $estAutomatique Indicateur de signalement automatique
     */
    public function setEstAutomatique(?bool $estAutomatique) : void {
        $this->estAutomatique = $estAutomatique;
    }

    /**
     * @brief Setter du contenu du signalement
     * 
     * @param string|null $contenu Contenu du signalement
     */
    public function setContenu(?string $contenu) : void {
        $this->contenu = $contenu;
    }
    
}