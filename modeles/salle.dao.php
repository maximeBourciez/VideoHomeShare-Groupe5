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
        $salle = new Salle($idSalle, $nom, $nbpersonne, $rangCourant, $code, $genre, $estPublique);
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

    
}
