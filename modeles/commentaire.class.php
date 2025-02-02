<?php

/**
 * @brief Classe représentant un commentaire utilisateur
 * 
 * Cette classe définit la structure d'un commentaire laissé par un utilisateur
 * sur un contenu ou une collection, avec ses propriétés et méthodes d'accès.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class Commentaire
{
    /** @var string|null Identifiant de l'utilisateur ayant posté le commentaire */
    private ?string $idUtilisateur;

    /** @var string|null Titre du commentaire */
    private ?string $titre;

    /** @var int|null Note attribuée (sur une échelle à définir) */
    private ?int $note;

    /** @var string|null Contenu textuel de l'avis */
    private ?string $avis;

    /** @var int|null Indicateur si l'avis est positif (1) ou négatif (0) */
    private ?int $estPositif;

    /** @var int|null Identifiant TMDB du contenu commenté */
    private ?int $idContenuTmdb;

    /** @var int|null Identifiant TMDB de la collection commentée */
    private ?int $idCollectionTmdb;

    /** @var int|null Identifiant TMDB de la série commentée */
    private ?int $idSerieTmdb;
    /**
     * @brief Constructeur de la classe Commentaire
     * 
     * @param string|null $idUtilisateur Identifiant de l'utilisateur
     * @param string|null $titre Titre du commentaire
     * @param int|null $note Note attribuée
     * @param string|null $avis Contenu du commentaire
     * @param bool|null $estPositif Indicateur de positivité
     * @param int|null $idContenuTmdb Identifiant du contenu TMDB
     * @param int|null $idCollectionTmdb Identifiant de la collection TMDB
     * @param int|null $idSerieTmdb Identifiant de la série TMDB
     */
    /** @var int|null Identifiant TMDB de la série commentée */

    public function __construct(
        ?string $idUtilisateur = null,
        ?string $titre = null,
        ?int $note = null,
        ?string $avis = null,
        ?bool $estPositif = null,
        ?int $idContenuTmdb = null,
        ?int $idCollectionTmdb = null,
        ?int $idSerieTmdb = null
    ) {
        $this->idUtilisateur = $idUtilisateur;
        $this->titre = $titre;
        $this->note = $note;
        $this->avis = $avis;
        $this->estPositif = $estPositif ? 1 : 0;
        $this->idContenuTmdb = $idContenuTmdb;
        $this->idCollectionTmdb = $idCollectionTmdb;
        $this->idSerieTmdb = $idSerieTmdb;
    }
    /**
     * @brief Récupère l'identifiant de l'utilisateur
     * @return string|null L'identifiant de l'utilisateur
     */
    public function getIdUtilisateur(): ?string
    {
        return $this->idUtilisateur;
    }

    /**
     * @brief Définit l'identifiant de l'utilisateur
     * @param string|null $idUtilisateur Le nouvel identifiant
     * @return self
     */
    public function setIdUtilisateur($idUtilisateur): self
    {
        $this->idUtilisateur = $idUtilisateur;
        return $this;
    }

    /**
     * @brief Récupère le titre du commentaire
     * @return string|null Le titre du commentaire
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * @brief Définit le titre du commentaire
     * @param string|null $titre Le nouveau titre
     * @return self
     */
    public function setTitre($titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    /**
     * @brief Récupère la note attribuée
     * @return int|null La note
     */
    public function getNote(): ?int
    {
        return $this->note;
    }

    /**
     * @brief Définit la note
     * @param int|null $note La nouvelle note
     * @return self
     */
    public function setNote($note): self
    {
        $this->note = $note;
        return $this;
    }

    /**
     * @brief Récupère le contenu de l'avis
     * @return string|null Le contenu de l'avis
     */
    public function getAvis(): ?string
    {
        return $this->avis;
    }

    /**
     * @brief Définit le contenu de l'avis
     * @param string|null $avis Le nouveau contenu
     * @return self
     */
    public function setAvis($avis): self
    {
        $this->avis = $avis;
        return $this;
    }

    /**
     * @brief Récupère l'indicateur de positivité
     * @return int|null 1 si positif, 0 si négatif, null si non défini
     */
    public function getEstPositif(): ?int
    {
        return $this->estPositif;
    }

    /**
     * @brief Définit l'indicateur de positivité
     * @param int|null $estPositif Le nouvel indicateur
     * @return self
     */
    public function setEstPositif($estPositif): self
    {
        $this->estPositif = $estPositif;
        return $this;
    }

    /**
     * @brief Récupère l'identifiant TMDB du contenu
     * @return int|null L'identifiant TMDB
     */
    public function getIdContenuTmdb(): ?int
    {
        return $this->idContenuTmdb;
    }

    /**
     * @brief Définit l'identifiant TMDB du contenu
     * @param int|null $idContenuTmdb Le nouvel identifiant
     * @return self
     */
    public function setIdContenuTmdb($idContenuTmdb): self
    {
        $this->idContenuTmdb = $idContenuTmdb;
        return $this;
    }

    /**
     * @brief Récupère l'identifiant TMDB de la collection commentée
     * @return int|null L'identifiant TMDB de la collection
     */
    public function getIdCollectionTmdb(): ?int
    {
        return $this->idCollectionTmdb;
    }

    /**
     * @brief Définit l'identifiant TMDB de la collection commentée
     * @param int|null $idCollectionTmdb Le nouvel identifiant de la collection
     * @return self
     */
    public function setIdCollectionTmdb(?int $idCollectionTmdb): self
    {
        $this->idCollectionTmdb = $idCollectionTmdb;
        return $this;
    }

    /**
     * @brief Récupère l'identifiant TMDB de la série commentée
     * @return int|null L'identifiant TMDB de la série
     */
    public function getIdSerieTmdb(): ?int
    {
        return $this->idSerieTmdb;
    }

    /**
     * @brief Définit l'identifiant TMDB de la série commentée
     * @param int|null $idSerieTmdb Le nouvel identifiant de la série
     * @return self
     */
    public function setIdSerieTmdb(?int $idSerieTmdb): self
    {
        $this->idSerieTmdb = $idSerieTmdb;
        return $this;
    }
}
