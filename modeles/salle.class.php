<?php

class Salle {


    /**
     * @brief idantifiant de la salle
     * @var int $idSalle 
     */
    private ?int $idSalle;
    /**
     * @brief nom de la salle
     * @var string $nom
     */
    private ?string $nom;
    /**
     * @brief nombre de personne maximume dans la salle
     * @var int $nbpersonne
     */
    private ?int $nbpersonne;
    /**
     * @brief nombre de place disponible dans la salle
     * @var int $placedisponible
     */
    private ?int $placedisponible;
    /**
     * @brief rang courant du contenu regader dans la salle
     * @var int $rangCourant
     */
    
    private ?int $rangCourant;
    /**
     * @brief code de la salle
     * @var int $code
     */
    private ?int $code;
    /**
     * @brief genre de la salle
     * @var string $genre
     */
    private ?string $genre;
    /**
     * @brief si la salle est publique ou non
     * @var bool $estPublique
     */
    private ?bool $estPublique;

    /**
     * @brief Constructeur de la classe Salle
     * @param mixed $idSalle
     * @param mixed $nom
     * @param mixed $nbpersonne
     * @param mixed $rangCourant
     * @param mixed $code
     * @param mixed $genre
     * @param mixed $estPublique
     */
    public function __construct(?int $idSalle = null, ?string $nom = null, ?int $nbpersonne= null, ?int $rangCourant= null, ?int $code = null, ?string $genre= null, ?bool $estPublique= null , ?int $placedisponible= null)
    {
        $this->idSalle = $idSalle;
        $this->nom = $nom;
        $this->nbpersonne = $nbpersonne;
        $this->rangCourant = $rangCourant;
        $this->code = $code;
        $this->genre = $genre;
        $this->estPublique = $estPublique;
        $this->placedisponible = $placedisponible;
    }

    // Getters
    /**
     * @brief Getter de l'identifiant de la salle
     * @return int
     */
    public function getIdSalle(): ?int
    {
        return $this->idSalle;
    }
    /**
     * @brief Getter du nom de la salle
     * @return string
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }
    /**
     * @brief Getter du nombre de personne maximume dans la salle
     * @return int
     */
    public function getNbPersonne(): ?int
    {
        return $this->nbpersonne;
    }
    /**
     * @brief Getter du nombre de place disponible
     * @return int
     */
    public function getPlaceDisponible(): ?int
    {
        return $this->placedisponible;
    }
    /**
     * @brief Getter du rang courant du contenu regader dans la salle
     * @return int
     */
    public function getRangCourant(): ?int
    {
        return $this->rangCourant;
    }
    /**
     * @brief Getter du code de la salle
     * @return int
     */
    public function getCode(): ?int
    {
        return $this->code;
    }
    /**
     * @brief Getter du genre de la salle
     * @return string
     */
    public function getGenre(): ?string
    {
        return $this->genre;
    }
    /**
     * @brief Getter de si la salle est publique ou non
     * @return bool
     */
    public function getEstPublique(): ?bool
    {
        return $this->estPublique;
    }

    // Setters
    
    /**
     * @brief Setter de l'identifiant de la salle
     * @param int $idSalle
     * @return void
     */
    public function setIdSalle(?int $idSalle): void
    {
        $this->idSalle = $idSalle;
    }
    /**
     * @brief Setter du nom de la salle
     * @param string $nom
     * @return void
     */
    public function setNom(?string $nom): void
    {
        $this->nom = $nom;
    }
    /**
     * @brief Setter du nombre de personne maximume dans la salle
     * @param int $nbpersonne
     * @return void
     */
    public function setNbPersonne(?int $nbpersonne): void
    {
        $this->nbpersonne = $nbpersonne;
    }
    /**
     * @brief Setter du nombre de place disponible dans la salle
     * @param int $placedisponible
     * @return void
     */
    public function setPlaceDisponible(?int $placedisponible): void
    {
        $this->placedisponible = $placedisponible;
    }
    /**
     * @brief Setter du rang courant du contenu regader dans la salle
     * @param int $rangCourant
     * @return void
     */
    public function setRangCourant(?int $rangCourant): void
    {
        $this->rangCourant = $rangCourant;
    }
    /**
     * @brief Setter du code de la salle
     * @param int $code
     * @return void
     */
    public function setCode(?int $code): void
    {
        $this->code = $code;
    }
    /**
     * @brief Setter du genre de la salle
     * @param string $genre
     * @return void
     */
    public function setGenre(?string $genre): void
    {
        $this->genre = $genre;
    }
    /**
     * @brief Setter de si la salle est publique ou non
     * @param bool $estPublique
     * @return void
     */
    public function setEstPublique(?bool $estPublique): void
    {
        $this->estPublique = $estPublique;
    } 



}