<?php

class ControllerTmdb extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * Affiche les informations d'un film depuis TMDB sans l'importer
     */
    public function afficherFilm(): void {
        $tmdbId = isset($_GET['tmdb_id']) ? intval($_GET['tmdb_id']) : 
                 (isset($_GET['id']) ? intval($_GET['id']) : null);
        if ($tmdbId) {
            // Initialiser l'API TMDB
            $tmdbApi = new TmdbAPI('a2096553592bde8ead1b2a0f2fa59bc0');
            
            // Récupérer les données du film
            $movieData = $tmdbApi->getMovieById($tmdbId);
            if ($movieData) {
                // Convertir en objet Contenu sans sauvegarder
                $contenu = $tmdbApi->convertToContenu($movieData);
                
                // Récupérer les personnalités
                $personnalites = $tmdbApi->getPersonnalites($movieData);
                
                // Récupérer les thèmes
                $themes = $tmdbApi->getGenres($movieData);
                
                // Récupérer les thèmes depuis la BD
                $themeDAO = new ThemeDAO($this->getPdo());
                $themesFromDB = [];
                
                // Pour chaque thème de TMDB
                foreach ($themes as $theme) {
                    $themeFromDB = $themeDAO->createIfNotExists($theme);
                    if ($themeFromDB) {
                        $themesFromDB[] = $themeFromDB;
                    }
                }
                
                // Afficher le template avec les données
                echo $this->getTwig()->render('pageDunFilm.html.twig', [
                    'contenu' => $contenu,
                    'personnalite' => $personnalites,
                    'themes' => $themesFromDB,
                    'moyenne' => 0,
                    'commentaires' => []
                ]);
                return;
            }
        }

        // Si pas d'ID ou film non trouvé, afficher le formulaire
        // echo $this->getTwig()->render('importerTmdb.html.twig');
    }
} 