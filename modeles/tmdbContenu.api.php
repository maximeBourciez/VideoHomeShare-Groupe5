<?php

class TmdbAPIContenu {
    private string $apiKey = TMDB_API_KEY;
    private string $baseUrl = TMDB_BASE_URL;
    private string $imageBaseUrl = TMDB_IMAGE_BASE_URL;
    private ?int $tmdbId = null;

    public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
    }

    /**
     * Récupère les détails d'un film par son ID TMDB
     */
    public function getMovieById(int $id): ?array {
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
    public function convertToContenu($movieData): Contenu {
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
            $companies = array_map(function($company) {
                return $company['name'];
            }, $movieData['production_companies']);
            $descriptionLongue .= implode(', ', $companies) . "\n\n";
        }
        
        // Ajouter des mots-clés si disponibles
        if (isset($movieData['keywords']['keywords']) && !empty($movieData['keywords']['keywords'])) {
            $descriptionLongue .= "Mots-clés : ";
            $keywords = array_map(function($keyword) {
                return $keyword['name'];
            }, $movieData['keywords']['keywords']);
            $descriptionLongue .= implode(', ', $keywords);
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
 * Récupère les personnalités (acteurs/réalisateurs) d'un film
 */
public function getPersonnalitesContenu(array $movieData): array {
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


    private function convertRuntime(int $minutes): string {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf("%02dh%02d", $hours, $mins);
    }

    private function makeRequest(string $url): ?array {
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

    public function getGenresContenu($movieData): array {
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

    public function getTmdbId(): ?int {
        return $this->tmdbId;
    }

    public function setTmdbId(?int $tmdbId): self {
        $this->tmdbId = $tmdbId;
        return $this;
    }
}
