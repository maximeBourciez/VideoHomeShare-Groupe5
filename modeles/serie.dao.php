<?php

/**
 * @brief Classe d'accès aux données pour les séries
 * 
 * Cette classe gère toutes les opérations de lecture des séries
 * depuis l'API TMDB
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class SerieDAO
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
     * @brief Constructeur de la classe SerieDAO
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
     * @brief Récupère une série depuis TMDB par son ID
     * 
     * @param int $tmdbId Identifiant TMDB de la série
     * @return Serie|null La série trouvée ou null si non trouvée
     */
    public function getSerieFromTMDB(int $tmdbId): ?Serie
    {
        $url = "{$this->baseUrl}/tv/{$tmdbId}?api_key={$this->apiKey}&language=fr-FR&include_adult=false&append_to_response=credits,keywords,reviews";
        $response = file_get_contents($url);

        if ($response === false) {
            return null;
        }

        $serieData = json_decode($response, true);

        if ($serieData && !($serieData['adult'] ?? false)) {
            return $this->convertToSerie($serieData);
        }

        return null;
    }

    /**
     * @brief Récupère les saisons d'une série
     * 
     * @param int $serieId ID de la série
     * @return array Liste des saisons
     */
    public function getSeasonsFromSerie(int $serieId): array
    {
        $serie = $this->getSerieFromTMDB($serieId);
        if (!$serie) {
            return [];
        }

        $url = "{$this->baseUrl}/tv/{$serieId}?api_key={$this->apiKey}&language=fr-FR";
        $serieData = $this->makeRequest($url);
        $seasons = [];

        if ($serieData && isset($serieData['seasons'])) {
            foreach ($serieData['seasons'] as $seasonData) {
                $seasons[] = new Saison(
                    $seasonData['id'],
                    $seasonData['name'],
                    $seasonData['overview'],
                    $seasonData['season_number'],
                    $seasonData['episode_count'],
                    !empty($seasonData['poster_path'])
                        ? $this->imageBaseUrl . $seasonData['poster_path']
                        : null,
                    new DateTime($seasonData['air_date'] ?? 'now')
                );
            }
        }

        return $seasons;
    }

    /**
     * @brief Recherche des séries par nom
     * 
     * @param string|null $query Terme de recherche
     * @return array|null Liste des séries trouvées ou null
     */
    public function searchByName(?string $query): ?array
    {
        $url = "{$this->baseUrl}/search/tv?api_key={$this->apiKey}&query=" . urlencode($query) . "&language=fr-FR";
        $response = file_get_contents($url);

        if ($response === false) {
            return null;
        }

        $seriesData = json_decode($response, true);

        if ($seriesData) {
            $series = [];
            foreach ($seriesData['results'] as $result) {
                $series[] = new Serie(
                    $result['id'],
                    $result['name'],
                    $result['overview'] ?? '',
                    new DateTime($result['first_air_date'] ?? 'now'),
                    !empty($result['poster_path'])
                        ? $this->imageBaseUrl . $result['poster_path']
                        : null,
                    $result['number_of_seasons'] ?? 0,
                    $result['number_of_episodes'] ?? 0
                );
            }
            return $series;
        }
        return null;
    }

    /**
     * @brief Effectue une requête HTTP vers l'API TMDB
     * 
     * @param string $url L'URL de la requête à effectuer
     * @return array|null Les données JSON décodées ou null en cas d'erreur
     */
    private function makeRequest(string $url): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode === 200 && $response) {
            return json_decode($response, true);
        }

        return null;
    }

    /**
     * @brief Convertit les données TMDB en objet Serie
     * 
     * @param array $serieData Les données brutes de la série depuis TMDB
     * @return Serie L'objet Serie créé à partir des données
     */
    private function convertToSerie(array $serieData): Serie
    {
        return new Serie(
            $serieData['id'],
            $serieData['name'],
            $serieData['overview'] ?? '',
            new DateTime($serieData['first_air_date'] ?? 'now'),
            !empty($serieData['poster_path'])
                ? $this->imageBaseUrl . $serieData['poster_path']
                : null,
            $serieData['number_of_seasons'] ?? 0,
            $serieData['number_of_episodes'] ?? 0
        );
    }

    /**
     * @brief Récupère les thèmes d'une série (maximum 5)
     * 
     * @param int $tmdbId Identifiant TMDB de la série
     * @return array Liste des thèmes (maximum 5 éléments)
     */
    public function getThemesSerie(int $tmdbId): array
    {
        $url = "{$this->baseUrl}/tv/{$tmdbId}/keywords?api_key={$this->apiKey}&language=fr-FR";
        $response = $this->makeRequest($url);

        $themes = [];
        if ($response && isset($response['results'])) {
            $count = 0;
            foreach ($response['results'] as $keyword) {
                if ($count >= 5) break; // Arrêter après 5 thèmes
                $themes[] = $keyword['name'];
                $count++;
            }
        }

        return $themes;
    }

    /**
     * @brief Récupère les personnalités principales d'une série
     * 
     * @param int $tmdbId Identifiant TMDB de la série
     * @return array Liste des personnalités avec leurs rôles, séparée en 'acteurs' et 'equipe'
     */
    public function getPersonnalitesSerie(int $tmdbId): array
    {
        $url = "{$this->baseUrl}/tv/{$tmdbId}/credits?api_key={$this->apiKey}&language=fr-FR";
        $response = $this->makeRequest($url);

        $personnalites = [
            'acteurs' => [],
            'equipe' => []
        ];

        if ($response) {
            // Récupérer les acteurs principaux (max 5)
            if (isset($response['cast'])) {
                foreach ($response['cast'] as $acteur) {
                    $personnalites['acteurs'][] = [
                        'nom' => $acteur['name'],
                        'role' => $acteur['character'],
                        'photo' => !empty($acteur['profile_path'])
                            ? $this->imageBaseUrl . $acteur['profile_path']
                            : null
                    ];
                }
            }

            // Récupérer les membres clés de l'équipe (réalisateur, scénariste, etc.)
            if (isset($response['crew'])) {
                $rolesRecherches = ['Director', 'Writer', 'Creator', 'Executive Producer'];
                foreach ($response['crew'] as $membre) {
                    if (in_array($membre['job'], $rolesRecherches)) {
                        $personnalites['equipe'][] = [
                            'nom' => $membre['name'],
                            'role' => $membre['job'],
                            'photo' => !empty($membre['profile_path'])
                                ? $this->imageBaseUrl . $membre['profile_path']
                                : null
                        ];
                    }
                }
            }
        }

        return $personnalites;
    }

    /**
     * @brief Récupère tous les épisodes d'une série
     * 
     * @param int $tmdbId Identifiant TMDB de la série
     * @return array Liste des épisodes groupés par saison avec les informations de chaque saison
     */
    public function getAllEpisodesFromSerie(int $tmdbId): array
    {
        $saisons = $this->getSeasonsFromSerie($tmdbId);
        $allEpisodes = [];

        foreach ($saisons as $saison) {
            $url = "{$this->baseUrl}/tv/{$tmdbId}/season/{$saison->getNumero()}?api_key={$this->apiKey}&language=fr-FR";
            $response = $this->makeRequest($url);

            if ($response && isset($response['episodes'])) {
                $episodes = [];
                foreach ($response['episodes'] as $episodeData) {
                    $episodes[] = new Episode(
                        $episodeData['id'],
                        $episodeData['name'],
                        $episodeData['overview'] ?? '',
                        $episodeData['episode_number'],
                        new DateTime($episodeData['air_date'] ?? 'now'),
                        !empty($episodeData['still_path'])
                            ? $this->imageBaseUrl . $episodeData['still_path']
                            : null
                    );
                }
                $allEpisodes[$saison->getNumero()] = [
                    'saison' => $saison,
                    'episodes' => $episodes
                ];
            }
        }

        return $allEpisodes;
    }
}
