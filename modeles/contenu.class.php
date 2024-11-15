<?php 

enum TypeContenu {
    case Episode;
    case Film;
}

class Contenu{
    private ?int $id;
    private ?string $titre;
    private ?DateTime $date;
    private ?string $description;
    private ?string $lienAffiche;
    private ?int $durée;
    private ?TypeContenu $type;
    //constructeur
    public function __construct(?int $id = null, ?string $titre = null, ?DateTime $date = null, ?string $description = null, ?string $lienAffiche = null, ?string $durée = null, ?TypeContenu $type = null){
        $this->id = $id;
        $this->titre = $titre;
        $this->date = $date;
        $this->description = $description;
        $this->lienAffiche = $lienAffiche;
        $this->durée = $durée;
        $this->type = $type;
    }
    //getters and setters
    //get id
    public function getId(): ?int{
        return $this->id;
    }
    // set id
    public function setId(?int $id): void{
        $this->id = $id;
    }

    //get titre
    public function getTitre(): ?string{
        return $this->titre;
    }
    // set titre
    public function setTitre(?string $titre): void{
        $this->titre = $titre;
    }

    //get date
    public function getDate() : ?DateTime{
        return $this->date;
    }
    // set date
    public function setDate(?DateTime $date): void{
        $this->date = $date;
    }
    //get description
    public function getDescription(): ?string{
        return $this->description;
    }
    // set description
    public function setDescription(?string $description): void{
        $this->description = $description;
    }
    //get lienAffiche
    public function getLienAffiche(): ?string{
        return $this->lienAffiche;
    }
    // set lienAffiche
    public function setLienAffiche(?string $lienAffiche): void{
        $this->lienAffiche = $lienAffiche;
    }
    //get durée
    public function getDurée(): ?int{
        return $this->durée;
    }
    // set durée
    public function setDurée(?int $durée): void{
        $this->durée = $durée;
    }
    //get type
    public function getType(): ?TypeContenu{
        return $this->type;
    }
    // set type
    public function setType(?TypeContenu $type): void{
        $this->type = $type;
    }

}