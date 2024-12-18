<?php 


class Contenu{   
    private ?int $id;
    private ?string $titre;
    private ?DateTime $date;
    private ?string $description;
    private ?string $DescriptionLongue;
    private ?string $lienAffiche;
    private ?string $type;
    private ?string $duree;
    private ?int $tmdbId;
  
    //constructeur
    public function __construct(?int $id = null, ?string $titre = null, ?DateTime $date = null, ?string $description = null, ?string $DescriptionLongue = null, ?string $lienAffiche = null, ?string $duree = null, ?string $type = null){
        $this->id = $id;
        $this->titre = $titre;
        $this->date = $date;
        $this->description = $description;
        $this->DescriptionLongue = $DescriptionLongue;
        $this->lienAffiche = $lienAffiche;
        $this->duree = $duree;
        $this->type = $type;
        $this->tmdbId = null;
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
    public function getDuree(): ?string{
        return $this->duree;
    }
    // set durée
    public function setDuree(?string $duree): void{
        $this->duree = $duree;
    }
    //get type
    public function getType(): ?string{
        return $this->type;
    }
    // set type
    public function setType(?string $type): void{
        $this->type = $type;
    }

    //get DescriptionLongue
    public function getDescriptionLongue(): ?string{
        return $this->DescriptionLongue;
    }

    // set DescriptionLongue
    public function setDescriptionLongue(?string $DescriptionLongue): void{
        $this->DescriptionLongue = $DescriptionLongue;
    }

    public function getTmdbId(): ?int {
        return $this->tmdbId;
    }

    public function setTmdbId(?int $tmdbId): self {
        $this->tmdbId = $tmdbId;
        return $this;
    }
}