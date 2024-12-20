<?php 


class Collection{   
    private ?int $id;
    private ?string $titreCollection;
    private ?DateTime $date;
    private ?string $description;
    private ?string $lienAffiche;
    private ?int $nombreFilms;
  
    //constructeur
    public function __construct(?int $id = null, ?string $titreCollection = null, ?DateTime $date = null, ?string $description = null, ?string $lienAffiche = null, ?int $nombreFilms = null){
        $this->id = $id;
        $this->titreCollection = $titreCollection;
        $this->date = $date;
        $this->description = $description;
        $this->lienAffiche = $lienAffiche;
        $this->nombreFilms = $nombreFilms;
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
    public function getTitreCollection(): ?string{
        return $this->titreCollection;
    }
    // set titre
    public function setTitreCollection(?string $titreCollection): void{
        $this->titreCollection = $titreCollection;
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
    public function getNombreFilms(): ?int{
        return $this->nombreFilms;
    }
    // set durée
    public function setNombreFilms(?int $nombreFilms): void{
        $this->nombreFilms = $nombreFilms;
    }
}