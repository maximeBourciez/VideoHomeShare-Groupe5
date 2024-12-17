<?php
class CommentaireContenu extends Commentaire {
    private ?string $idContenu;

    public function __construct(
        ?string $idUtilisateur, 
        ?string $titre, 
        ?int $note, 
        ?string $avis, 
        ?int $estPositif, 
        ?string $idContenu
    ) {
        parent::__construct($idUtilisateur, $titre, $note, $avis, $estPositif);
        $this->idContenu = $idContenu;
    }

    /**
     * Get the value of idContenu
     */
    public function getIdContenu(){
        return $this->idContenu;
    }

    /**
     * Set the value of idContenu
     *
     * @return  self
     */
    public function setIdContenu($idContenu){
        $this->idContenu = $idContenu;

        return $this;
    }
}
?>