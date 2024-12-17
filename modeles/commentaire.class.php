<?php

class Commentaire {
    private ?string $idUtilisateur;
    private ?string $titre;
    private ?int $note;
    private ?string $avis;
    private ?int $estPositif;

    public function __construct(
        ?string $idUtilisateur, 
        ?string $titre, 
        ?int $note, 
        ?string $avis, 
        ?int $estPositif
    ) {
        $this->idUtilisateur = $idUtilisateur;
        $this->titre = $titre;
        $this->note = $note;
        $this->avis = $avis;
        $this->estPositif = $estPositif;
    }

    

    /**
     * Get the value of idUtilisateur
     */ 
    public function getIdUtilisateur()
    {
        return $this->idUtilisateur;
    }

    /**
     * Set the value of idUtilisateur
     *
     * @return  self
     */ 
    public function setIdUtilisateur($idUtilisateur)
    {
        $this->idUtilisateur = $idUtilisateur;

        return $this;
    }

    /**
     * Get the value of titre
     */ 
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * Set the value of titre
     *
     * @return  self
     */ 
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get the value of note
     */ 
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set the value of note
     *
     * @return  self
     */ 
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get the value of avis
     */ 
    public function getAvis()
    {
        return $this->avis;
    }

    /**
     * Set the value of avis
     *
     * @return  self
     */ 
    public function setAvis($avis)
    {
        $this->avis = $avis;

        return $this;
    }

    /**
     * Get the value of estPositif
     */ 
    public function getEstPositif()
    {
        return $this->estPositif;
    }

    /**
     * Set the value of estPositif
     *
     * @return  self
     */ 
    public function setEstPositif($estPositif)
    {
        $this->estPositif = $estPositif;

        return $this;
    }
}