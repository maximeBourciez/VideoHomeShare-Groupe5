<?php
class CommentaireCollection extends Commentaire {
    private ?string $idCollection;

    public function __construct(
        ?string $idUtilisateur, 
        ?string $titre, 
        ?int $note, 
        ?string $avis, 
        ?int $estPositif, 
        ?string $idCollection
    ) {
        parent::__construct($idUtilisateur, $titre, $note, $avis, $estPositif);
        $this->idCollection = $idCollection;
    }

    /**
     * Get the value of idCollection
     */
    public function getIdCollection(){
        return $this->idCollection;
    }

    /**
     * Set the value of idCollection
     *
     * @return  self
     */
    public function setIdCollection($idCollection){
        $this->idCollection = $idCollection;

        return $this;
    }
}
?>