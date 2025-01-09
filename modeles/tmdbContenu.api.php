<?php

class TmdbAPIContenu
{
    private string $apiKey = TMDB_API_KEY;
    private string $baseUrl = TMDB_BASE_URL;
    private string $imageBaseUrl = TMDB_IMAGE_BASE_URL;
    private ?int $tmdbId = null;

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Récupère les détails d'un film par son ID TMDB
     */
    public function getMovieById(int $id): ?array
    {
        $url = "https://api.themoviedb.org/3/movie/{$id}?api_key={$this->apiKey}&language=fr-FR&append_to_response=keywords,credits,reviews";

        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }

        $movieData = json_decode($response, true);

        // Vérification si le film est marqué comme "adult"
        if (isset($movieData['adult']) && $movieData['adult'] === true) {
            return null;
        }

        return $movieData;
    }

    /**
     * Convertit un film TMDB en objet Contenu
     */
    public function convertToContenu($movieData): Contenu
    {
        $date = new DateTime($movieData['release_date']);

        // Description courte : utiliser overview
        $descriptionCourte = $movieData['overview'] ?? '';

        // Description longue : combiner plusieurs éléments
        $descriptionLongue = '';

        // Ajouter le tagline s'il existe
        if (!empty($movieData['tagline'])) {
            $descriptionLongue .= $movieData['tagline'] . "\n\n";
        }

        // Ajouter l'overview
        $descriptionLongue .= $movieData['overview'] . "\n\n";

        // Ajouter des informations supplémentaires si disponibles
        if (isset($movieData['reviews']['results']) && !empty($movieData['reviews']['results'])) {
            $descriptionLongue .= "Critiques :\n";
            foreach (array_slice($movieData['reviews']['results'], 0, 2) as $review) {
                $descriptionLongue .= "- " . $review['content'] . "\n\n";
            }
        }

        // Ajouter des informations sur la production
        if (!empty($movieData['production_companies'])) {
            $descriptionLongue .= "Production : ";
            $companies = array_map(function ($company) {
                return $company['name'];
            }, $movieData['production_companies']);
            $descriptionLongue .= implode(', ', $companies) . "\n\n";
        }

        // Création des liens d'images avec différentes tailles
        $lienAffiche = !empty($movieData['poster_path'])
            ? "https://image.tmdb.org/t/p/original" . $movieData['poster_path']
            : null;

        $lienAfficheReduite = !empty($movieData['poster_path'])
            ? "https://image.tmdb.org/t/p/w185" . $movieData['poster_path']
            : null;

        $contenu = new Contenu(
            null,                // id
            $movieData['title'], // titre
            $date,              // date
            $descriptionCourte, // description
            $descriptionLongue, // descriptionLongue
            $lienAffiche,       // lienAffiche
            $movieData['runtime'], // duree
            'Film',             // type
            $lienAfficheReduite // lienAfficheReduite
        );

        return $contenu;
    }


    /**
     * Convertit un film TMDB en objet ContenuLeger sans certaines informations.
     */
    public function convertToContenuLight($movieData): Contenu
    {
        // Description courte : utiliser overview
        $descriptionCourte = $movieData['overview'] ?? '';

        $lienAfficheReduite = !empty($movieData['poster_path'])
            ? "https://image.tmdb.org/t/p/w185" . $movieData['poster_path']
            : null;

        $contenu = new Contenu(
            $movieData['id'],    // id
            $movieData['title'], // titre
            null,
            $descriptionCourte,
            null,
            null,
            null,
            'Film',             // type
            $lienAfficheReduite // lienAfficheReduite
        );

        return $contenu;
    }
    /**
     * Récupère les personnalités (acteurs/réalisateurs) d'un film
     */
    public function getPersonnalitesContenu(array $movieData): array
    {
        $personnalites = [];

        // Ajouter les réalisateurs
        foreach ($movieData['credits']['crew'] as $crew) {
            if ($crew['job'] === 'Director') {
                $personnalites[] = new Personnalite(
                    null,
                    $crew['name'], // nom
                    '', // prenom
                    !empty($crew['profile_path'])
                    ? $this->imageBaseUrl . $crew['profile_path']
                    : null,
                    'Réalisateur'
                );
            }
        }

        // Ajouter les acteurs principaux
        $actors = array_slice($movieData['credits']['cast'], 0, 17);
        foreach ($actors as $actor) {
            $personnalites[] = new Personnalite(
                null,
                $actor['name'], // nom
                '', // prenom
                !empty($actor['profile_path'])
                ? $this->imageBaseUrl . $actor['profile_path']
                : null,
                'Acteur'
            );
        }
        return $personnalites;
    }


    private function convertRuntime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf("%02dh%02d", $hours, $mins);
    }

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

    public function getGenresContenu($movieData): array
    {
        $themes = [];
        if (isset($movieData['genres'])) {
            foreach ($movieData['genres'] as $genre) {
                $themes[] = new Theme(
                    null,
                    $genre['name'],
                    null
                );
            }
        }
        return $themes;
    }

    public function getTmdbId(): ?int
    {
        return $this->tmdbId;
    }

    public function setTmdbId(?int $tmdbId): self
    {
        $this->tmdbId = $tmdbId;
        return $this;
    }

    /**
     * Récupère les films tendance du moment
     * 
     * @param string $timeWindow Peut être "day" ou "week" (par défaut : "day")
     * @return array|null Liste des films tendance ou null si une erreur se produit
     */
    public function getTrendingMovies(string $timeWindow = 'day'): ?array
    {
        $url = "https://api.themoviedb.org/3/trending/movie/{$timeWindow}?api_key={$this->apiKey}&language=fr-FR";

        // Récupérer les résultats
        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }

        $moviesData = json_decode($response, true);

        if ($moviesData === null || !isset($moviesData['results'])) {
            return null;
        } else {
            // Convertir les films en objets Contenu
            $contenus = [];
            foreach ($moviesData['results'] as $movieData) {
                $contenus[] = $this->convertToContenuLight($movieData);
            }
            return $contenus;
        }
    }

    /**
     * Récupère les films populaires pour un genre spécifique.
     *
     * @param int $genreId ID du genre à filtrer (par exemple, 28 pour Action, 12 pour Aventure)
     * @param int $limit Nombre de films à récupérer (par défaut 20)
     * @return array|null Liste des films populaires ou null en cas d'erreur
     */
    public function getPopularMoviesByGenre(int $genreId, int $limit = 20): ?array
    {
        $url = "https://api.themoviedb.org/3/discover/movie?api_key={$this->apiKey}&language=fr-FR&sort_by=popularity.desc&with_genres={$genreId}&page=1";

        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }

        $moviesData = json_decode($response, true);

        if ($moviesData === null || !isset($moviesData['results'])) {
            return null;
        } else {
            // Convertir les films en objets Contenu
            $contenus = [];
            foreach ($moviesData['results'] as $movieData) {
                $contenus[] = $this->convertToContenuLight($movieData);
            }
            return array_slice($contenus, 0, $limit);
        }
    }


    /**
     * @brief Méthode de recherche de films dans l'api TMDB
     * 
     * @param string $query La recherche à effectuer
     * 
     * @return array|null Liste des films trouvés ou null en cas d'erreur 
     */
    public function searchMoviesByName(string $query): ?array
    {
        // Construire l'URL pour la recherche par nom
        $url = "https://api.themoviedb.org/3/search/movie?api_key={$this->apiKey}&language=fr-FR&query=" . urlencode($query);

        // Récupérer les résultats
        $response = file_get_contents($url);
        if ($response === false) {
            return null; // En cas d'erreur, retourner null
        }

        $moviesData = json_decode($response, true);

        // Vérifier si les résultats sont valides
        if ($moviesData === null || !isset($moviesData['results'])) {
            return null; // Si pas de résultats, retourner null
        } else {
            // Convertir les films en objets Contenu
            $contenus = [];
            foreach ($moviesData['results'] as $movieData) {
                $contenus[] = $this->convertToContenuLight($movieData);
            }
            return $contenus;
        }

    }

}
