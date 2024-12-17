<?php

  
enum TypeCollection{
    case Saga;
    case Serie;
}
/**
 * Classe représentant une collection.
 *
 * Cette classe permet de définir et de manipuler les informations relatives à une collection,
 * telles que son identifiant, son type, son nom et le lien de son affiche.
 */
class Collection {
    
    /**
     * @var int|null $id Identifiant unique de la collection.
     */
    private ?int $id; 
    
    /**
     * @var string|null $type Type de la collection.
     */
    private ?TypeCollection $type; 
    
    /**
     * @var string|null $nom Nom de la collection.
     */
    private ?string $nom; 
    
    /**
     * @var string|null $lienAffiche Lien de l'affiche de la collection.
     */
    private ?string $lienAffiche; 

    /**
     * Constructeur de la classe Collection.
     *
     * Initialise une collection avec les valeurs optionnelles pour l'identifiant, le type,
     * le nom et le lien de l'affiche.
     *
     * @param int|null $id Identifiant de la collection (par défaut null).
     * @param string|null $type Type de la collection (par défaut null).
     * @param string|null $nom Nom de la collection (par défaut null).
     * @param string|null $lienAffiche Lien vers l'affiche de la collection (par défaut null).
     */
    public function __construct(?int $id = null, ?string $type = null, ?string $nom = null, ?string $lienAffiche = null){



class Collection{
    // Attributs 
    private ?int $id; //Identifiant de la collection
    private ?TypeCollection $type; //Type de collection
    private ?string $nom; //Nom de la collection

    // Constructeur
    public function __construct(?int $id = null, ?TypeCollection $type = null, ?string $nom = null){
        $this->id = $id;
        $this->type = $type;
        $this->nom = $nom;
        $this->lienAffiche = $lienAffiche;
    }
    
    /**
     * Récupère l'identifiant de la collection.
     *
     * @return int|null L'identifiant de la collection.
     */
    public function getId(): ?int{
        return $this->id;
    }

    public function getType(): ?TypeCollection{
        return $this->type;
    }
    
    /**
     * Récupère le nom de la collection.
     *
     * @return string|null Le nom de la collection.
     */
    public function getNom(): ?string{
        return $this->nom;
    }
    
    /**
     * Récupère le lien de l'affiche de la collection.
     *
     * @return string|null Le lien de l'affiche de la collection.
     */
    public function getLienAffiche(): ?string{
        return $this->lienAffiche;
    }

    /**
     * Définit l'identifiant de la collection.
     *
     * @param int|null $id L'identifiant de la collection.
     */
    public function setId(?int $id) : void{
        $this->id = $id;
    }
    
    /**
     * Définit le type de la collection.
     *
     * @param TypeCollection|null $type Le type de la collection.
     */

    public function setType(?TypeCollection $type) : void{
        $this->type = $type;
    }
    
    /**
     * Définit le nom de la collection.
     *
     * @param string|null $nom Le nom de la collection.
     */
    public function setNom(?string $nom) : void{
        $this->nom = $nom;
    }
    
    /**
     * Définit le lien de l'affiche de la collection.
     *
     * @param string|null $lienAffiche Le lien de l'affiche de la collection.
     */
    public function setLienAffiche(?string $lienAffiche) : void{
        $this->lienAffiche = $lienAffiche;
    }

}

?>
