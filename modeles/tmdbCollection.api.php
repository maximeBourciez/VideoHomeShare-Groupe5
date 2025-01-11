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

    /**
     * Récupère les personnalités d'une collection par son ID ou objet Collection
     * @param int|Collection $collection ID de la collection ou objet Collection
     */
    public function getPersonnalitesCollection($collection): array {
        // Extraire l'ID si un objet Collection est passé
        $collectionId = is_object($collection) ? $collection->getId() : $collection;
        
        if (!is_int($collectionId)) {
            return [];
        }

        $url = "https://api.themoviedb.org/3/collection/{$collectionId}?api_key={$this->apiKey}&language=fr-FR&append_to_response=credits";
        
        $response = file_get_contents($url);
        if ($response === false) {
            return [];
        }
        
        $collectionData = json_decode($response, true);
        $personnalites = [];
        
        if (isset($collectionData['parts'])) {
            foreach ($collectionData['parts'] as $movie) {
                // Récupérer les détails du film pour avoir les crédits
                $movieUrl = "https://api.themoviedb.org/3/movie/{$movie['id']}?api_key={$this->apiKey}&language=fr-FR&append_to_response=credits";
                $movieResponse = file_get_contents($movieUrl);
                if ($movieResponse !== false) {
                    $movieData = json_decode($movieResponse, true);
                    
                    // Ajouter les réalisateurs
                    if (isset($movieData['credits']['crew'])) {
                        foreach ($movieData['credits']['crew'] as $crew) {
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
                    }
                    
                    // Ajouter les acteurs principaux
                    if (isset($movieData['credits']['cast'])) {
                        $actors = array_slice($movieData['credits']['cast'], 0, 5);
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
        }
        
        // Éliminer les doublons en comparant les noms
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
     * Récupère les détails de tous les films d'une collection
     */
    public function getMoviesFromCollection(int $collectionId): array {
        $collection = $this->getCollectionById($collectionId);
        if (!$collection) {
            return [];
        }
        
        $movies = [];
        $description = $collection->getDescription();
        preg_match_all('/- (.+) \((\d{4})\) \[(\d+)\]/', $description, $matches);

        if (!empty($matches[1])) {
            $tmdbContenuApi = new TmdbAPIContenu($this->apiKey);
            
            for ($i = 0; $i < count($matches[1]); $i++) {
                $movieId = $matches[3][$i];
                $movieData = $tmdbContenuApi->getMovieById($movieId);
                
                if ($movieData) {
                    $contenu = $tmdbContenuApi->convertToContenuLight($movieData);
                    $contenu->setTmdbId($movieId);
                    $movies[] = $contenu;
                }
            }
        }

        return $movies;
    }

    /**
     * Méthode permettant de rechercher des collections par nom
     * 
     * @param string|null $query Terme de recherche
     * 
     * @return array<Collection>|null Collections trouvées
     */
    public function searchCollectionsByName(?string $query){
        // Encodage du nom pour l'URL
        $encodedName = urlencode($query);
        $url = "https://api.themoviedb.org/3/search/collection?api_key={$this->apiKey}&language=fr-FR&query={$encodedName}";
        
        // Récupération des données
        $response = @file_get_contents($url);
        if ($response === false) {
            // Gérer l'erreur si l'API ne répond pas
            return null;
        }

        $collectionData = json_decode($response, true);
        if ($collectionData === null || isset($collectionData['status_code'])) {
            // Vérification si la réponse est invalide ou s'il y a une erreur API
            return null;
        }

        // Vérification si la collection est marquée comme "adult"
        if (isset($collectionData['adult']) && $collectionData['adult'] === true) {
            return null;
        }

        // Convertir les données en objet Collection
        return $this->convertToMultipleCollection($collectionData);
    }

    /**
     * Convertit les données TMDB en tableau d'objets Collection
     * 
     * @param array $collectionData Données TMDB
     * 
     * @return array<Collection> Tableau d'objets Collection
     */
    private function convertToMultipleCollection(array $collectionData): array {
        $collections = [];
        foreach ($collectionData as $collection) {
            $collections[] = $this->convertToCollection($collection);
        }
        return $collections;
    }
}
