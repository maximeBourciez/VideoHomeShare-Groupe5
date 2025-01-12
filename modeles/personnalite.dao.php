<?php

/**
 * @brief Classe d'accès aux données pour les personnalités
 * 
 * Cette classe gère toutes les opérations de lecture et d'écriture
 * des personnalités dans la base de données. Elle permet de récupérer
 * les personnalités individuellement ou en relation avec des contenus.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class PersonnaliteDAO
{
    /** @var PDO|null Instance de connexion à la base de données */
    private ?PDO $pdo;

    /**
     * @brief Constructeur de PersonnaliteDAO
     * 
     * @param PDO|null $pdo Instance de connexion à la base de données
     */
    function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Trouve une personnalité par son identifiant
     * 
     * @param int $id Identifiant de la personnalité
     * @return Personnalite|null La personnalité trouvée ou null si non trouvée
     */
    function find(int $id): ?Personnalite
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "personnalite WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $personnalite = $stmt->fetch();
        return $this->hydrate($personnalite);
    }

    /**
     * @brief Récupère toutes les personnalités
     * 
     * @return array Liste de toutes les personnalités
     */
    function findAll(): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "personnalite";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Trouve toutes les personnalités liées à un contenu
     * 
     * Cette méthode récupère toutes les personnalités (acteurs, réalisateurs, etc.)
     * associées à un contenu spécifique via la table de jointure.
     * 
     * @param int $id Identifiant du contenu
     * @return array Liste des personnalités associées au contenu
     */
    function findAllParContenuId(int $id): array
    {
        $sql = "SELECT * 
                FROM " . DB_PREFIX . "personnalite AS pe 
                JOIN " . DB_PREFIX . "participer AS pa ON pa.idPersonaliter = pe.idPersonaliter 
                WHERE pa.idContenu = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Hydrate une personnalité à partir d'un tableau de données
     * 
     * @param array|false $data Données de la personnalité
     * @return Personnalite|null La personnalité hydratée ou null si données invalides
     */
    private function hydrate($data): ?Personnalite
    {
        if ($data === false) {
            return null;
        }
        
        return new Personnalite(
            $data['id'] ?? null,
            $data['nom'] ?? null,
            $data['prenom'] ?? null,
            $data['urlImage'] ?? null,
            $data['role'] ?? null
        );
    }

    /**
     * @brief Hydrate plusieurs personnalités à partir d'un tableau de données
     * 
     * @param array $dataArray Tableau de données des personnalités
     * @return array Liste des personnalités hydratées
     */
    private function hydrateAll(array $dataArray): array
    {
        $personnalites = [];
        foreach ($dataArray as $data) {
            $personnalite = $this->hydrate($data);
            if ($personnalite !== null) {
                $personnalites[] = $personnalite;
            }
        }
        return $personnalites;
    }
}
