<?php
/**
 * @file fil.class.php
 * 
 * @brief Classe Fil
 *
 *  @details Cette classe permet de gérer les fils de discussion du forum
 * 
 * @date 12/11/2024
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */ 
class Fil{
    // Attributs
    /**
     * @var integer|null $id Identifiant du fil
     */
    private ?int $id;
    /**
     * @var string|null $titre Titre du fil
     */
    private ?string $titre;
    /**
     * @var DateTime|null $dateCreation Date de création du fil
     */
    private ?DateTime $dateCreation; 
    /**
     * @var string|null $description Description du fil
     */
    private ?string $description; 
    /**
     * @var Utilisateur|null $utilisateur Utilisateur qui a créé le fil
     */
    private ?Utilisateur $utilisateur; 
    /**
     * @var null|Theme[] $themes Thèmes associés au fil
     */
    private ?array $themes;


    // Constructeur
    /**
     * @brief Constructeur de la classe Fil
     * 
     * @param integer|null $id Identifiant du fil
     * @param string|null $titre Titre du fil
     * @param DateTime|null $dateCreation Date de création du fil
     * @param string|null $description Description du fil
     * @param Utilisateur|null $utilisateur Utilisateur qui a créé le fil
     * @param Theme[]|null $themes Thèmes associés au fil
     */
    public function __construct(?int $id, ?string $titre, ?DateTime $dateCreation, ?string $description, ?Utilisateur $utilisateur = null, ?array $themes = null){
        $this->id = $id;
        $this->titre = $titre;
        $this->dateCreation = $dateCreation;
        $this->description = $description;
        $this->utilisateur = $utilisateur;
        $this->themes = $themes;
    }


    // Encapsulation
    // Getters
    /**
     * @brief Getter de l'identifiant du fil
     * @return integer|null Identifiant du fil
     */
    public function getId(): ?int{
        return $this->id;
    }

    /**
     * @brief Getter du titre du fil
     * @return string|null Titre du fil
     */
    public function getTitre(): ?string{
        return $this->titre;
    }

    /**
     * @brief Getter de la date de création du fil
     * @return DateTime|null Date de création du fil
     */
    public function getDateCreation(): ?DateTime{
        return $this->dateCreation;
    }

    /**
     * @brief Getter de la description du fil
     * @return string|null Description du fil
     */
    public function getDescription(): ?string{
        return $this->description;
    }

    /**
     * @brief Getter de l'utilisateur ayant créé le fil
     * @return Utilisateur|null Utilisateur ayant créé le fil
     */
    public function getUtilisateur(): ?Utilisateur{
        return $this->utilisateur;
    }

    /**
     * @brief Getter des thèmes associés au fil
     * @return Theme[]|null Thèmes associés au fil
     */
    public function getThemes(): ?array{
        return $this->themes;
    }



    // Setters
    /**
     * @brief Setter de l'identifiant du fil
     * @param integer $id Identifiant du fil
     * @return void
     */
    public function setId(int $id){
        $this->id = $id;
    }

    /**
     * @brief Setter du titre du fil
     * @param string $titre Titre du fil
     * @return void
     */
    public function setTitre(string $titre){
        $this->titre = $titre;
    }

    /**
     * @brief Setter de la date de création du fil
     * @param DateTime $dateCreation Date de création du fil
     * @return void
     */
    public function setDateCreation(DateTime $dateCreation){
        $this->dateCreation = $dateCreation;
    }

    /**
     * @brief Setter de la description du fil
     * @param string $description Description du fil
     * @return void
     */
    public function setDescription(string $description){
        $this->description = $description;
    }

    /**
     * @brief Setter de l'utilisateur ayant créé le fil
     * @param Utilisateur $utilisateur Utilisateur ayant créé le fil
     * @return void
     */
    public function setUtilisateur(Utilisateur $utilisateur){
        $this->utilisateur = $utilisateur;
    }

    /**
     * @brief Setter des thèmes associés au fil
     * @param Theme[] $themes Thèmes associés au fil
     * @return void
     */
    public function setThemes(array $themes){
        $this->themes = $themes;
    }


    // Méthodes
    /**
     * @brief Méthode pour ajouter un thème au fil
     * 
     * @param Theme $theme Thème à ajouter
     * @return void
     */
    public function ajouterTheme(Theme $theme){
        $this->themes[] = $theme;
    }

    /**
     * @brief Méthode pour supprimer un thème du fil
     * 
     * @param Theme $theme Thème à supprimer
     * @return void
     */
    public function supprimerTheme(Theme $theme){
        $key = array_search($theme, $this->themes);
        if($key !== false){
            unset($this->themes[$key]);
        }
    }

    

}