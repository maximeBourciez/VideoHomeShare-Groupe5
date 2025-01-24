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
        $url = "{$this->baseUrl}/tv/{$tmdbId}?api_key={$this->apiKey}&language=fr-FR&append_to_response=credits,keywords,reviews";
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

    public function getEpisodesFromSerie(int $tmdbId, int $saison): array
    {
        $url = "{$this->baseUrl}/tv/{$tmdbId}/season/{$saison}?api_key={$this->apiKey}&language=fr-FR";
        return $this->makeRequest($url);
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
     * @return array Liste des thèmes (max 5)
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
}
