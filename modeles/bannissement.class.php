<?php





/**
 * @file bannissement.classe.php
 * 
 * @brief classe Bannissement
 * 
 * @version 1.0
 * 
 * @date 14/02/2025
 * 
 * @author Sylvain Trouilh <strouilh@iutbayonne.univ-pau.fr>
 */


 

class Bannissement {

    /**
     * @brief identifiant  du bannissement
     * @var int $id
     */
    private ?int $id;
    /**
     * @brief raison du bannissement
     * @var string $raison
     */
    private ?string $raison;
    /**
     * @brief date de début du bannissement
     * @var DateTime $dateB
     */
    private ?DateTime $dateB;
    /**
     * @brief identifiant de l'utilisateur banni
     * @var  string $idUtilisateur
     */
    private ?string $idUtilisateur;


    public function __construct(?int $id = null, ?string $raison = null, ?DateTime $dateB = null, ?string $idUtilisateur = null)
    {
        $this->id = $id;
        $this->raison = $raison;
        $this->dateB = $dateB;
        $this->idUtilisateur = $idUtilisateur;
    }

    //geter 
    /**
     * @brief getter de l'identifiant
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * @brief getter de la raison
     * @return string|null
     */
    public function getRaison(): ?string
    {
        return $this->raison;
    }
    /**
     * @brief getter de la date de début
     * @return DateTime|null
     */
    public function getDateB(): ?DateTime
    {
        return $this->dateB;
    }
    /**
     * @brief getter de l'identifiant de l'utilisateur
     * @return string|null
     */
    public function getIdUtilisateur(): ?string
    {
        return $this->idUtilisateur;
    }

    //seter
    /**
     * @brief setter de l'identifiant
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }
    /**
     * @brief setter de la raison
     * @param string|null $raison
     */
    public function setRaison(?string $raison): void
    {
        $this->raison = $raison;
    }
    /**
     * @brief setter de la date de début
     * @param DateTime|null $dateB
     */
    public function setDateB(?DateTime $dateB): void
    {
        $this->dateB = $dateB;
    }
    /**
     * @brief setter de l'identifiant de l'utilisateur
     * @param string|null $idUtilisateur
     */
    public function setIdUtilisateur(?string $idUtilisateur): void
    {
        $this->idUtilisateur = $idUtilisateur;
    }




}