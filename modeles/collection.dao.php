<?php

/**
 * @brief Classe d'accès aux données pour les collections
 * 
 * Cette classe gère toutes les opérations de lecture et d'écriture
 * des collections, que ce soit depuis la base de données ou l'API TMDB
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class CollectionDAO
{
    /** @var PDO|null Instance de connexion à la base de données */
    private ?PDO $pdo;

    /** @var string Clé d'API pour l'accès à TMDB */
    private string $apiKey;

    /** @var string URL de base de l'API TMDB */
    private string $baseUrl;

    /** @var string URL de base pour les images TMDB */
    private string $imageBaseUrl;

    /**
     * @brief Constructeur de la classe CollectionDAO
     * 
     * @param PDO|null $pdo Instance de connexion à la base de données
     * @param string $apiKey Clé API TMDB
     */
    public function __construct(?PDO $pdo = null, string $apiKey = TMDB_API_KEY)
    {
        $this->pdo = $pdo;
        $this->apiKey = $apiKey;
        $this->baseUrl = TMDB_BASE_URL;
        $this->imageBaseUrl = TMDB_IMAGE_BASE_URL;
    }

    /**
     * @brief Récupère une collection depuis TMDB par son ID
     * 
     * @param int $tmdbId Identifiant TMDB de la collection
     * @return Collection|null La collection trouvée ou null si non trouvée
     */
    public function getCollectionFromTMDB(int $tmdbId): ?Collection
    {
        $url = "{$this->baseUrl}/collection/{$tmdbId}?api_key={$this->apiKey}&language=fr-FR&append_to_response=keywords,credits,reviews";
        $response = file_get_contents($url);

        if ($response === false) {
            return null; // En cas d'erreur, retourner null
        }

        $collectionData = json_decode($response, true);

        if ($collectionData && !($collectionData['adult'] ?? false)) {
            return $this->convertToCollection($collectionData);
        }

        return null;
    }

    /**
     * @brief Récupère les personnalités d'une collection
     * 
     * @param int|Collection $collection ID ou objet Collection
     * @return array Liste des personnalités
     */
    public function getPersonnalitesCollection($collection): array
    {
        $collectionId = is_object($collection) ? $collection->getId() : $collection;

        if (!is_int($collectionId)) {
            return [];
        }

        $url = "{$this->baseUrl}/collection/{$collectionId}?api_key={$this->apiKey}&language=fr-FR&append_to_response=credits";
        $collectionData = $this->makeRequest($url);
        $personnalites = [];

        if (isset($collectionData['parts'])) {
            foreach ($collectionData['parts'] as $movie) {
                $movieUrl = "{$this->baseUrl}/movie/{$movie['id']}?api_key={$this->apiKey}&language=fr-FR&append_to_response=credits";
                $movieData = $this->makeRequest($movieUrl);

                if ($movieData) {
                    // Ajouter réalisateurs
                    foreach ($movieData['credits']['crew'] ?? [] as $crew) {
                        if ($crew['job'] === 'Director') {
                            $personnalites[] = new Personnalite(
                                null,
                                $crew['name'],
                                '',
                                !empty($crew['profile_path'])
                                    ? $this->imageBaseUrl . $crew['profile_path']
                                    : null,
                                'Réalisateur'
                            );
                        }
                    }

                    // Ajouter acteurs principaux
                    $actors = array_slice($movieData['credits']['cast'] ?? [], 0, 5);
                    foreach ($actors as $actor) {
                        $personnalites[] = new Personnalite(
                            null,
                            $actor['name'],
                            '',
                            !empty($actor['profile_path'])
                                ? $this->imageBaseUrl . $actor['profile_path']
                                : null,
                            'Acteur'
                        );
                    }
                }
            }
        }

        // Éliminer doublons
        return $this->removeDuplicatePersonnalites($personnalites);
    }

    /**
     * @brief Récupère les films d'une collection
     * 
     * @param int $collectionId ID de la collection
     * @return array Liste des contenus de la collection
     */
    public function getMoviesFromCollection(int $collectionId): array
    {
        $collection = $this->getCollectionFromTMDB($collectionId);
        if (!$collection) {
            return [];
        }

        $movies = [];
        $description = $collection->getDescription();
        preg_match_all('/- (.+) \((\d{4})\) \[(\d+)\]/', $description, $matches);

        if (!empty($matches[1])) {
            $contenuDAO = new ContenuDAO($this->pdo, $this->apiKey);

            for ($i = 0; $i < count($matches[1]); $i++) {
                $movieId = $matches[3][$i];
                $contenu = $contenuDAO->getContentFromTMDB($movieId);
                if ($contenu) {
                    $movies[] = $contenu;
                }
            }
        }

        return $movies;
    }


    /**
     * @brief Récupère une collection par son identifiant
     * 
     * Cette méthode recherche une collection dans la base de données
     * à partir de son ID et retourne un objet Collection si trouvé
     * 
     * @param int $id Identifiant de la collection à rechercher
     * @return Collection|null La collection trouvée ou null si non trouvée
     */
    public function getCollectionById(int $id): ?Collection
    {
        $sql = 'SELECT * FROM collections WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Collection(
                $result['id'],
                $result['titre'],
                new DateTime($result['date']),
                $result['description'],
                $result['lienAffiche'],
                $result['nombreFilms']
            );
        }

        return null;
    }

    /**
     * @brief Effectue une requête HTTP vers l'API TMDB
     */
    private function makeRequest(string $url): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode === 200) {
            return json_decode($response, true);
        }

        return null;
    }

    /**
     * @brief Convertit les données TMDB en objet Collection
     */
    private function convertToCollection(array $collectionData): Collection
    {
        $date = new DateTime('now');
        if (!empty($collectionData['release_date'])) {
            try {
                $date = new DateTime($collectionData['release_date']);
            } catch (Exception $e) {
                // Garder la date par défaut
            }
        }

        return new Collection(
            $collectionData['id'],
            $collectionData['name'],
            $date,
            $this->formatDescription($collectionData),
            !empty($collectionData['poster_path'])
                ? 'https://image.tmdb.org/t/p/original' . $collectionData['poster_path']
                : null,
            count($collectionData['parts'] ?? [])
        );
    }

    /**
     * @brief Formate la description d'une collection
     */
    private function formatDescription(array $collectionData): string
    {
        $description = '';

        if (!empty($collectionData['overview'])) {
            $description .= $collectionData['overview'] . "\n\n";
        }

        if (!empty($collectionData['parts'])) {
            $description .= "Films de la collection :\n";
            foreach ($collectionData['parts'] as $film) {
                $date = !empty($film['release_date']) ? ' (' . substr($film['release_date'], 0, 4) . ')' : '';
                $description .= "- " . $film['title'] . $date . " [" . $film['id'] . "]\n";
            }
        }

        return trim($description);
    }

    /**
     * @brief Élimine les doublons de personnalités
     */
    private function removeDuplicatePersonnalites(array $personnalites): array
    {
        $uniquePersonnalites = [];
        $seenNames = [];

        foreach ($personnalites as $personnalite) {
            if (!in_array($personnalite->getNom(), $seenNames)) {
                $seenNames[] = $personnalite->getNom();
                $uniquePersonnalites[] = $personnalite;
            }
        }

        return $uniquePersonnalites;
    }

    /**
     * Vérifie si une collection contient des films pour adultes.
     * Cette méthode interroge l'API TMDB pour obtenir les détails d'une collection
     * et vérifie si l'un des films qu'elle contient est marqué comme contenu adulte.
     *
     * @param int $collectionId L'identifiant TMDB de la collection à vérifier
     * 
     * @return bool Retourne true si la collection contient au moins un film pour adultes,
     *              false sinon ou en cas d'erreur de l'API
     * 
     * @throws Exception En cas d'erreur grave lors de la requête à l'API
     */
    private function hasAdultMovies(int $collectionId): bool
    {
        try {
            $url = "{$this->baseUrl}/collection/{$collectionId}?api_key={$this->apiKey}&language=fr-FR";

            $context = stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'timeout' => 5
                ]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return false; // En cas d'erreur, on considère que ce n'est pas une collection adulte
            }

            $data = json_decode($response, true);
            if (!$data || !isset($data['parts'])) {
                return false;
            }

            // Vérifier chaque film de la collection
            foreach ($data['parts'] as $movie) {
                if (isset($movie['adult']) && $movie['adult']) {
                    return true;
                }
            }

            return false;
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification des films adultes: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Recherche des collections par leur nom dans l'API TMDB.
     * Cette méthode filtre automatiquement les collections contenant du contenu pour adultes
     * et gère les cas d'erreur de l'API.
     *
     * @param string|null $query Le terme de recherche pour trouver les collections.
     *                          Si null ou vide, la méthode retournera null.
     * 
     * @return array<Collection>|null Retourne un tableau d'objets Collection correspondant aux critères
     *                               de recherche, ou null si aucun résultat n'est trouvé ou en cas d'erreur
     * 
     * @throws Exception En cas d'erreur grave lors de la requête à l'API
     * 
     * @example
     * // Rechercher toutes les collections contenant "Star Wars"
     * $collections = $collectionDAO->searchByName("Star Wars");
     * 
     * // Gérer le cas où aucun résultat n'est trouvé
     * if ($collections === null) {
     *     echo "Aucune collection trouvée";
     * }
     */
    public function searchByName(?string $query): ?array
    {
        if (empty($query)) {
            return null;
        }

        try {
            $url = "{$this->baseUrl}/search/collection?api_key={$this->apiKey}&query=" . urlencode($query) . "&language=fr-FR&include_adult=false";

            $context = stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'timeout' => 5
                ]
            ]);

            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                return null;
            }

            $collectionData = json_decode($response, true);
            if (!$collectionData || empty($collectionData['results'])) {
                return null;
            }

            $collections = [];
            foreach ($collectionData['results'] as $result) {
                if (!isset($result['id'])) {
                    continue;
                }

                // On n'appelle hasAdultMovies que si nécessaire
                // et on ignore simplement les collections qui causent une erreur 404
                try {
                    if ($this->hasAdultMovies($result['id'])) {
                        continue;
                    }
                } catch (Exception $e) {
                    continue;
                }

                $collections[] = new Collection(
                    $result['id'],
                    $result['name'] ?? 'Sans titre',
                    null,
                    $result['overview'] ?? null,
                    isset($result['poster_path']) ? "https://image.tmdb.org/t/p/w500" . $result['poster_path'] : null,
                    null
                );
            }

            return !empty($collections) ? $collections : null;
        } catch (Exception $e) {
            error_log("Exception lors de la recherche de collection: " . $e->getMessage());
            return null;
        }
    }
}
