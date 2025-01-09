<?php

/**
 * @file fil.dao.php
 * 
 * @brief Classe FilDAO
 * 
 * @details Classe permettant de gérer les accès à la base de données pour les fils de discussion
 * 
 * @date 12/11/2024
 * 
 * @version 1.0
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */
class FilDAO
{
    // Attributs
    /**
     * @var PDO|null $pdo Connexion à la base de données
     */
    private ?PDO $pdo;

    // Constructeur
    /**
     * @brief Constructeur de la classe FilDAO
     * 
     * @param PDO|null $pdo Connexion à la base de données
     */
    public function __construct(?PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Encapsulation
    // Getter
    /**
     * @brief Getter de la connexion à la base de données
     * @return PDO|null Connexion à la base de données
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    // Setter
    /**
     * @brief Setter de la connexion à la base de données
     * @param PDO $pdo Connexion à la base de données
     * @return self
     */
    public function setPdo(PDO $pdo): self
    {
        $this->pdo = $pdo;
        return $this;
    }

    // Méthodes
    // Méthodes d'hydratation
    /**
     * @brief Méthode d'hydratation d'un fil
     * 
     * @param array $row Ligne de la base de données
     * @return Fil Objet Fil hydraté
     */
    public function hydrate(array $row): Fil
    {
        $fil = new Fil();
        // Création de l'objet Theme
        $themes = [];

        if (isset($row[0]) && is_array($row[0])) {
            // Création de l'objet Utilisateur
            $user = new Utilisateur();
            $user->setId($row[0]['idUtilisateur']);
            $user->setPseudo($row[0]['pseudo']);
            $user->setUrlImageProfil($row[0]['urlImageProfil']);

            // Création de l'objet Fil 
            $id = $row[0]['idFil'];
            $titre = $row[0]['titre'];
            $dateCreation = new DateTime($row[0]['dateC']);
            $description = $row[0]['description'];

            foreach ($row as $element) {
                if ($element['idFil'] == $id) {
                    $theme = new Theme($element['theme_id'], $element['theme_nom']);
                    $themes[] = $theme;
                }
            }

            $fil = new Fil($id, $titre, $dateCreation, $description, $user, $themes);
            return $fil;
        } else {
            // Création de l'objet Utilisateur
            $user = new Utilisateur();
            $user->setId($row['idUtilisateur']);
            $user->setPseudo($row['pseudo']);
            $user->setUrlImageProfil($row['urlImageProfil']);

            // Création de l'objet Fil 
            $id = $row['idFil'];
            $titre = $row['titre'];
            $dateCreation = new DateTime($row['dateC']);
            $description = $row['description'];

            $theme = new Theme($row['theme_id'], $row['theme_nom']);
            $themes[] = $theme;

            // Ajout des likes dans les cas ou ils existent
            $nbLikes = $row['likes'] ?? 0;

            $fil = new Fil($id, $titre, $dateCreation, $description, $user, $themes, $nbLikes);
            return $fil;
        }
    }

    /**
     * @brief Méthode d'hydratation de tous les fils
     * 
     * @param array $rows Tableau de lignes de la base de données
     * @return array<Fil> Tableau d'objets Fil hydratés
     */
    function hydrateAll(array $rows): array
    {
        $fils = [];
        foreach ($rows as $row) {
            if (isset($fils[$row['idFil']])) {
                // Ajout du thème au fil
                $theme = new Theme($row['theme_id'], $row['theme_nom']);
                $fils[$row['idFil']]->ajouterTheme($theme);
            }
            // Sinon
            else {
                $fils[$row['idFil']] = $this->hydrate($row);
            }
        }
        return $fils;
    }

    // Méthodes de recherche 
    /**
     * @brief Méthode pour lister tous les fils avec le premier utilisateur ayant posté
     * 
     * @return array<Fil> Tableau d'objets Fil
     */

    public function findAll(): array
    {
        $sql = "
            SELECT DISTINCT f.*,
            u.idUtilisateur AS idUtilisateur,
            u.pseudo AS pseudo,
            u.urlImageProfil AS urlImageProfil,
            t.idTheme AS theme_id,
            t.nom AS theme_nom
            FROM " . DB_PREFIX . "fil AS f
            LEFT JOIN (
                SELECT m.idFil, MIN(m.dateC) AS firstDate
                FROM " . DB_PREFIX . "message AS m
                GROUP BY m.idFil
            ) AS first_message ON f.idFil = first_message.idFil
            LEFT JOIN " . DB_PREFIX . "message AS m ON f.idFil = m.idFil AND m.dateC = first_message.firstDate
            LEFT JOIN " . DB_PREFIX . "utilisateur AS u ON m.idUtilisateur = u.idUtilisateur
            LEFT JOIN " . DB_PREFIX . "parlerdeTheme AS p ON f.idFil = p.idFil
            LEFT JOIN " . DB_PREFIX . "theme AS t ON p.idTheme = t.idTheme
            ORDER BY f.idFil DESC";

        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Méthode pour trouver un fil par son id
     * 
     * @details Méthode permettant de trouver un fil par son id - Sert à afficher un fil de discussion et ses messages sous-jacents
     *
     * @param integer $id Identifiant du fil
     * @return Fil|null
     */
    public function findById(int $id): ?Fil
    {
        $sql = "
            SELECT DISTINCT f.*, 
               u.idUtilisateur, 
               u.pseudo, 
               u.urlImageProfil, 
               t.idTheme AS theme_id, 
               t.nom AS theme_nom
                FROM " . DB_PREFIX . "fil AS f
                LEFT JOIN (
                    SELECT m.idFil, MIN(m.dateC) AS firstDate
                    FROM " . DB_PREFIX . "message AS m
                    GROUP BY m.idFil
                ) AS first_message ON f.idFil = first_message.idFil
                LEFT JOIN " . DB_PREFIX . "message AS m 
                    ON f.idFil = m.idFil AND m.dateC = first_message.firstDate
                LEFT JOIN " . DB_PREFIX . "utilisateur AS u 
                    ON m.idUtilisateur = u.idUtilisateur
                LEFT JOIN " . DB_PREFIX . "parlerdeTheme AS p 
                    ON f.idFil = p.idFil
                LEFT JOIN " . DB_PREFIX . "theme AS t 
                    ON p.idTheme = t.idTheme
                WHERE f.idFil = :id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrate($stmt->fetchAll());
    }


    /**
     * @brief Méthode pour trouver les messages d'un fil par son id
     *
     * @param integer $idFil
     * @return array
     * @warning non testée
     */
    public function findMessagesByFilId(int $idFil): array
    {
        $sql = "
            SELECT m.*, u.idUtilisateur, u.pseudo, u.urlImageProfil
            FROM " . DB_PREFIX . "message AS m
            INNER JOIN " . DB_PREFIX . "utilisateur AS u ON m.idUtilisateur = u.idUtilisateur
            WHERE m.idFil = :idFil
            ORDER BY m.dateC ASC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idFil', $idFil, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $messages = new MessageDAO($this->pdo);
        return $messages->listerMessagesParFil($idFil);
    }

    /**
     * @brief Méthode pour récupérer l'utilisateur ayant posté le premier message d'un fil
     * 
     * @param integer $idFil Identifiant du fil
     * 
     * @return Utilisateur|null
     */
    public function findFirstUserByFilId(int $idFil): ?Utilisateur
    {
        $sql = "
            SELECT u.*
            FROM " . DB_PREFIX . "message AS m
            INNER JOIN " . DB_PREFIX . "utilisateur AS u ON m.idUtilisateur = u.idUtilisateur
            WHERE m.idFil = :idFil
            ORDER BY m.dateC ASC
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':idFil', $idFil, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $user = new UtilisateurDAO($this->pdo);
        return $user->hydrate($stmt->fetch());
    }

    /**
     * @brief Méthode pour ajouter un fil
     * 
     * @param string $titre Titre du fil
     * @param string $description Description du fil
     * @param int $idUtilisateur Identifiant de l'utilisateur
     * 
     * @return int Identifiant du fil ajouté
     */
    public function create(string $titre, string $description): int
    {
        $sql = "
            INSERT INTO " . DB_PREFIX . "fil (titre, dateC, description)
            VALUES (:titre, NOW(), :description)
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titre', $titre, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->execute();
        return $this->pdo->lastInsertId();
    }

    /** 
     * @brief Méthode pour ajouter un thème à un fil
     * 
     * @param int $idFil Identifiant du fil
     * @param array<int> $themes Tableau des id des thèmes sélectionnés  
     * 
     * @return void
     */
    public function addThemes(int $idFil, array $themes): void
    {
        $sql = "
            INSERT INTO " . DB_PREFIX . "parlerdeTheme (idFil, idTheme)
            VALUES (:idFil, :idTheme)
        ";
        $stmt = $this->pdo->prepare($sql);
        foreach ($themes as $theme) {
            $stmt->bindValue(':idFil', $idFil, PDO::PARAM_INT);
            $stmt->bindValue(':idTheme', intval($theme), PDO::PARAM_INT);
            $stmt->execute();
        }
    }

    /**
     * @brief Méthode pour récupérer les fils les plus likés
     * 
     * @param int $limit Nombre de fils à récupérer
     * 
     * @return array<Fil> Tableau d'objets Fil
     */
    public function getFilsLesPlusLikes(int $limit): array
    {
        $sql = "
                SELECT DISTINCT f.*, 
                    u.idUtilisateur, 
                    u.pseudo, 
                    u.urlImageProfil, 
                    t.idTheme AS theme_id, 
                    t.nom AS theme_nom,
                    likes.likes AS likes
                FROM " . DB_PREFIX . "fil AS f
                LEFT JOIN (
                    SELECT m.idFil, COUNT(l.idMessage) AS likes
                    FROM " . DB_PREFIX . "message AS m
                    LEFT JOIN " . DB_PREFIX . "reagir AS l ON m.idMessage = l.idMessage
                    GROUP BY m.idFil
                ) AS likes ON f.idFil = likes.idFil
                LEFT JOIN (
                    SELECT m.idFil, MIN(m.dateC) AS firstDate
                    FROM " . DB_PREFIX . "message AS m
                    GROUP BY m.idFil
                ) AS first_message ON f.idFil = first_message.idFil
                LEFT JOIN " . DB_PREFIX . "message AS m 
                    ON f.idFil = m.idFil AND m.dateC = first_message.firstDate
                LEFT JOIN " . DB_PREFIX . "utilisateur AS u 
                    ON m.idUtilisateur = u.idUtilisateur
                LEFT JOIN " . DB_PREFIX . "parlerdeTheme AS p 
                    ON f.idFil = p.idFil
                LEFT JOIN " . DB_PREFIX . "theme AS t 
                    ON p.idTheme = t.idTheme
                GROUP BY f.idFil
                ORDER BY likes.likes DESC
                LIMIT :limit
            ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll());
    }

    /**
     * @brief Méthode pour récupérer les fils résulatnts d'une recherche
     * 
     * @param string $search Recherche
     * 
     * @return array<Fil> Tableau d'objets Fil
     */
    public function searchFils(string $search): array
    {
        $sql = "
            SELECT DISTINCT f.*, 
                u.idUtilisateur, 
                u.pseudo, 
                u.urlImageProfil, 
                t.idTheme AS theme_id, 
                t.nom AS theme_nom
            FROM " . DB_PREFIX . "fil AS f
            LEFT JOIN (
                SELECT m.idFil, MIN(m.dateC) AS firstDate
                FROM " . DB_PREFIX . "message AS m
                GROUP BY m.idFil
            ) AS first_message ON f.idFil = first_message.idFil
            LEFT JOIN " . DB_PREFIX . "message AS m 
                ON f.idFil = m.idFil AND m.dateC = first_message.firstDate
            LEFT JOIN " . DB_PREFIX . "utilisateur AS u 
                ON m.idUtilisateur = u.idUtilisateur
            LEFT JOIN " . DB_PREFIX . "parlerdeTheme AS p 
                ON f.idFil = p.idFil
            LEFT JOIN " . DB_PREFIX . "theme AS t 
                ON p.idTheme = t.idTheme
            WHERE f.titre LIKE :search
            OR f.description LIKE :search
            OR t.nom LIKE :search
            ORDER BY f.idFil DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $this->hydrateAll($stmt->fetchAll());
    }
}
