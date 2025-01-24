<?php


class SalleDAO{

    // Attiributs 
    /**
     * @var PDO|null $pdo Connexion à la BD
     */
    private ?PDO $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe MessageDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getter
    /**
     * @brief Getter de la connexion à la base de données
     *
     * @return PDO|null Connexion à la base de données
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    // Setter
    /**
     * @brief Setter de la connexion à la base de données
     * 
     * @param PDO $pdo Connexion à la base de données
     * @return self
     */
    public function setPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }


    //Methodes
    /**
     * @brief hydrate un objet salle
     * 
     * @return array $row tableau contenant les informations de la salle
     */
    function hydrate(array $row): Salle
    {
        $idSalle = $row['idSalle'];
        $nom = $row['nom'];
        $nbpersonne = $row['nbpersonne'];
        $rangCourant = $row['rangCourant'];
        $code = $row['code'];
        $genre = $row['genre'];
        $estPublique = $row['estPublique'];
        $placedisponible = $row['placedisponible'];
        $salle = new Salle($idSalle, $nom, $nbpersonne, $rangCourant, $code, $genre, $estPublique, $placedisponible);
        return $salle;
    }

    /**
     * @brief hydrate un tableau d'objets salle
     * 
     * @return array $rows tableau contenant les informations des salles
     */
    function hydrateAll(array $rows): array
    {
        $salles = [];
        foreach ($rows as $row) {
            $salles[] = $this->hydrate($row);
        }
        return $salles;
    }


    /**
     * @brief Trouve une salle par son id
     * 
     * @param int $id id de la salle
     * @return Salle salle trouvée
     */
    function find(int $id): Salle
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX. "salle WHERE idSalle = :id");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $this->hydrate($row);
    }

    /**
     * @brief Trouve toutes les salles
     * 
     * @return array tableau contenant toutes les salles
     */
    function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX. "salle");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return $this->hydrateAll($rows);
    }

    /**
     * @brief Trouve toutes les salles publiques
     * 
     * @return array tableau contenant toutes les salles publiques
     */
    function findAllPublique(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX. "salle WHERE estPublique = 1");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return $this->hydrateAll($rows);
    }

    function update(Salle $salle): void
    {
        $stmt = $this->pdo->prepare("UPDATE ".DB_PREFIX. "salle SET nom = :nom, nbpersonne = :nbpersonne, rangCourant = :rangCourant, code = :code, genre = :genre, estPublique = :estPublique, placedisponible = :placedisponible WHERE idSalle = :idSalle");
        $stmt->bindValue(':nom', $salle->getNom());
        $stmt->bindValue(':nbpersonne', $salle->getNbPersonne());
        $stmt->bindValue(':rangCourant', $salle->getRangCourant());
        $stmt->bindValue(':code', $salle->getCode());
        $stmt->bindValue(':genre', $salle->getGenre());

        if($salle->getEstPublique() == true){
            $stmt->bindValue(':estPublique', 1);
        }else{
            $stmt->bindValue(':estPublique', 0);
        }
        $stmt->bindValue(':placedisponible', $salle->getPlaceDisponible());
        $stmt->bindValue(':idSalle', $salle->getIdSalle());
        $stmt->execute();
    }

    function findByCode(string $code): Salle
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX. "salle WHERE code = :code");
        $stmt->bindValue(':code', $code);
        $stmt->execute();
        $row = $stmt->fetch();
        return $this->hydrate($row);
    }

    function findAllPubliqueDisponible(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ".DB_PREFIX. "salle WHERE estPublique = 1 AND placedisponible > 0");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return $this->hydrateAll($rows);
    }

    function create(Salle $salle): bool
    {
        
        $stmt = $this->pdo->prepare("INSERT INTO ".DB_PREFIX. "salle (nom, nbpersonne, rangCourant, code, genre, estPublique, placedisponible) VALUES (:nom, :nbpersonne, :rangCourant, :code, :genre, :estPublique, :placedisponible)");
        $stmt->bindValue(':nom', $salle->getNom());
        $stmt->bindValue(':nbpersonne', $salle->getNbPersonne());
        $stmt->bindValue(':rangCourant', $salle->getRangCourant());
        $stmt->bindValue(':code', $salle->getCode());
        $stmt->bindValue(':genre', $salle->getGenre());
        if($salle->getEstPublique() == true){
            $stmt->bindValue(':estPublique', 1);
        }else{
            $stmt->bindValue(':estPublique', 0);
        }
        $stmt->bindValue(':placedisponible', $salle->getPlaceDisponible());
        return($stmt->execute());
    }

    function ajouterRole( ?string $idUtilisateur, ?int $idSalle, ?string $role){
        $stmt = $this->pdo->prepare("INSERT INTO ".DB_PREFIX. "se_trouver (idUtilisateur, idSalle, role) VALUES (:idUtilisateur, :idSalle, :role)");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur);
        $stmt->bindValue(':idSalle', $idSalle);
        $stmt->bindValue(':role', $role);
        return($stmt->execute());

    }

    function findRole( ?string $idUtilisateur, ?int $idSalle)
    {
        $stmt = $this->pdo->prepare("SELECT role FROM ".DB_PREFIX. "se_trouver WHERE idUtilisateur = :idUtilisateur AND idSalle = :idSalle");
        $stmt->bindValue(':idUtilisateur', $idUtilisateur);
        $stmt->bindValue(':idSalle', $idSalle);
        $stmt->execute();
        $row = $stmt->fetch();
        if ($row == false){
            return null;
        }
        return $row['role'];
    }

    function suprimersalle(int $id) : bool{
        $stmt = $this->getPdo()->prepare("DELETE FROM ".DB_PREFIX."salle WHERE idSalle = :idSalle ");
        $stmt->bindParam(":idSalle", $id);
        return $stmt->execute();
    }
        
        

        
}

    

