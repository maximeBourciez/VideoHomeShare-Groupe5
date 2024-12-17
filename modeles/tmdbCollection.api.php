<?php

class TmdbAPICollection {
    private string $apiKey = TMDB_API_KEY;
    private string $baseUrl = TMDB_BASE_URL;
    private string $imageBaseUrl = TMDB_IMAGE_BASE_URL;
    private CollectionDAO $collectionDAO;

    public function __construct(string $apiKey, CollectionDAO $collectionDAO) {
        $this->apiKey = $apiKey;
        $this->collectionDAO = $collectionDAO;
    }

    /**
     * Récupère les détails d'une collection par son ID TMDB et retourne un objet Collection
     */
    public function getCollectionById(int $id): ?Collection {
        $url = "https://api.themoviedb.org/3/collection/{$id}?api_key={$this->apiKey}&language=fr-FR&append_to_response=keywords,credits,reviews";
        
        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }
        
        $collectionData = json_decode($response, true);
        
        // Vérification si la collection est marquée comme "adult"
        if (isset($collectionData['adult']) && $collectionData['adult'] === true) {
            return null;
        }

        // Convertir les données TMDB en objet Collection
        return $this->convertToCollection($collectionData);
    }

    /**
     * Convertit les données TMDB en objet Collection
     */
    private function convertToCollection(array $collectionData): Collection {
        // Gestion de la date
        $date = new DateTime('now');
        if (!empty($collectionData['release_date'])) {
            try {
                $date = new DateTime($collectionData['release_date']);
            } catch (Exception $e) {
                // En cas d'erreur, on garde la date actuelle
            }
        }

        return new Collection(
            null,
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
     * Formate la description de la collection
     */
    private function formatDescription(array $collectionData): string {
        $description = '';
        
        // Ajouter l'overview principal
        if (!empty($collectionData['overview'])) {
            $description .= $collectionData['overview'] . "\n\n";
        }
        
        // Ajouter la liste des films
        if (!empty($collectionData['parts'])) {
            $description .= "Films de la collection :\n";
            foreach ($collectionData['parts'] as $film) {
                $date = !empty($film['release_date']) ? ' (' . substr($film['release_date'], 0, 4) . ')' : '';
                $description .= "- " . $film['title'] . $date . "\n";
            }
        }

        return trim($description);
    }

    /**
     * Crée ou met à jour une collection dans la base de données
     */
    public function saveCollection(Collection $collection): ?Collection {
        // Vérifier si la collection existe déjà
        $existingCollection = $this->collectionDAO->getCollectionById($collection->getId() ?? -1);
        
        if ($existingCollection) {
            // Mettre à jour la collection existante
            if ($this->collectionDAO->updateCollection($collection)) {
                return $collection;
            }
        } else {
            // Créer une nouvelle collection
            if ($this->collectionDAO->createCollection($collection)) {
                return $collection;
            }
        }
        
        return null;
    }

    /**
     * Récupère une collection complète (API + DB) par ID TMDB
     */
    public function getCompleteCollection(int $tmdbId): ?Collection {
        // D'abord, essayer de récupérer depuis l'API TMDB
        $collection = $this->getCollectionById($tmdbId);
        
        if ($collection === null) {
            return null;
        }

        // Sauvegarder ou mettre à jour dans la base de données
        return $this->saveCollection($collection);
    }

    /**
     * Récupère les genres des films dans une collection
     */
    public function getGenresFromCollection(int $tmdbId): array {
        $collection = $this->getCollectionById($tmdbId);
        if ($collection === null) {
            return [];
        }

        $genres = [];
        if (!empty($collection->getDescription())) {
            $films = explode("\n", $collection->getDescription());
            foreach ($films as $film) {
                if (preg_match('/- (.+) \((\d{4})\)/', $film, $matches)) {
                    $filmTitle = $matches[1];
                    $filmYear = $matches[2];
                    $filmGenres = $this->getGenresForFilm($filmTitle, $filmYear);
                    $genres = array_merge($genres, $filmGenres);
                }
            }
        }

        return array_unique($genres);
    }

    /**
     * Récupère les genres pour un film donné par son titre et année
     */
    private function getGenresForFilm(string $title, string $year): array {
        $url = "https://api.themoviedb.org/3/search/movie?api_key={$this->apiKey}&query=" . urlencode($title) . "&year={$year}&language=fr-FR";
        $response = file_get_contents($url);
        if ($response === false) {
            return [];
        }

        $data = json_decode($response, true);
        if (!empty($data['results'])) {
            $movieId = $data['results'][0]['id'];
            $movieDetailsUrl = "https://api.themoviedb.org/3/movie/{$movieId}?api_key={$this->apiKey}&language=fr-FR";
            $movieDetailsResponse = file_get_contents($movieDetailsUrl);
            if ($movieDetailsResponse !== false) {
                $movieDetails = json_decode($movieDetailsResponse, true);
                return array_column($movieDetails['genres'], 'name');
            }
        }

        return [];
    }
}
