<?php

/**
 * @brief Classe d'accès aux données pour les contenus
 * 
 * Cette classe gère toutes les opérations de lecture et d'écriture
 * des contenus, que ce soit depuis la base de données ou l'API TMDB.
 * Elle centralise l'accès aux données des contenus multimédias.
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class ContenuDAO
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
     * @brief Constructeur de ContenuDAO
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
     * @brief Récupère un contenu depuis TMDB par son ID
     * 
     * Cette méthode interroge l'API TMDB pour obtenir les informations
     * détaillées d'un film et les convertit en objet Contenu.
     * Les films pour adultes sont automatiquement filtrés.
     * 
     * @param int $tmdbId Identifiant TMDB du film
     * @return Contenu|null Le contenu trouvé ou null si non trouvé ou filtré
     */
    public function getContentFromTMDB(int $tmdbId): ?Contenu
    {
        $url = "{$this->baseUrl}/movie/{$tmdbId}?api_key={$this->apiKey}&language=fr-FR&append_to_response=keywords,credits,reviews";
        // Récupérer les résultats
        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }

        $moviesData = json_decode($response, true);

        if ($moviesData === null ) {
            return null;
        } else {
            // Convertir les films en objets Contenu
            return $this->convertToContenu($moviesData);
        }
    }

    /**
     * @brief Récupère les personnalités associées à un contenu
     * 
     * Extrait les acteurs et réalisateurs des données TMDB et les
     * convertit en objets Personnalite.
     * 
     * @param array $movieData Données brutes du film depuis TMDB
     * @return array Liste des objets Personnalite
     */
    public function getPersonnalites(array $movieData): array
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

    /**
     * @brief Récupère les genres d'un contenu
     * 
     * Convertit les genres TMDB en objets Theme locaux.
     * 
     * @param array $movieData Données brutes du film depuis TMDB
     * @return array Liste des objets Theme
     */
    public function getGenres(array $movieData): array
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

    /**
     * @brief Convertit les données TMDB en objet Contenu
     * 
     * Transforme les données brutes de l'API en un objet Contenu structuré
     * en incluant les descriptions, images et métadonnées.
     * 
     * @param array $movieData Données brutes du film
     * @return Contenu L'objet Contenu créé
     */
    private function convertToContenu(array $movieData): Contenu
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
            null,                   // id
            $movieData['title'],    // titre
            $date,                  // date
            $descriptionCourte,     // description
            $descriptionLongue,     // descriptionLongue
            $lienAffiche,          // lienAffiche
            $movieData['runtime'],  // duree
            'Film',                // type
            $lienAfficheReduite    // lienAfficheReduite
        );

        // Ajouter l'ID TMDB
        $contenu->setTmdbId($movieData['id']);

        return $contenu;
    }


    public function convertToContenuLight($movieData): Contenu
    {
        // Description courte : utiliser overview
        $descriptionCourte = $movieData['overview'] ?? '';

        $lienAfficheReduite = !empty($movieData['poster_path'])
            ? "https://image.tmdb.org/t/p/w185" . $movieData['poster_path']
            : null;

        $contenu = new Contenu(
            $movieData['id'], // id
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
     * @brief Effectue une requête HTTP vers l'API TMDB
     * 
     * Gère la communication avec l'API TMDB et le décodage de la réponse.
     * 
     * @param string $url URL complète de la requête API
     * @return array|null Données décodées ou null en cas d'erreur
     */
    private function makeRequest(string $url): ?array
    {
        // Récupérer les résultats
        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * @brief Convertit une durée en minutes en format lisible
     * 
     * Transforme une durée en minutes en format "HHhMM"
     * 
     * @param int $minutes Nombre de minutes
     * @return string Durée formatée (ex: "02h15")
     */
    private function convertRuntime(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf("%02dh%02d", $hours, $mins);
    }

    /**
     * @brief Récupère les données brutes d'un film depuis TMDB
     * 
     * Cette méthode fait une requête à l'API TMDB pour obtenir
     * toutes les données brutes d'un film
     * 
     * @param int $tmdbId Identifiant TMDB du film
     * @return array|null Les données brutes du film ou null si non trouvé
     */
    public function getMovieData(int $tmdbId): ?array
    {
        $url = "{$this->baseUrl}/movie/{$tmdbId}?api_key={$this->apiKey}&language=fr-FR&append_to_response=keywords,credits,reviews";
        return $this->makeRequest($url);
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