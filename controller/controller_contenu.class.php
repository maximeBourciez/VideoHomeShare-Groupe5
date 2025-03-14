<?php

/**
 * @brief Contrôleur gérant l'affichage des contenus
 * 
 * Cette classe gère l'affichage des informations détaillées des films
 * en utilisant le DAO pour accéder aux données
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class ControllerContenu extends Controller {
    /** @var ContenuDAO Instance du DAO pour l'accès aux données des contenus */
    private ContenuDAO $contenuDAO;

    /**
     * @brief Constructeur du contrôleur de contenu
     * 
     * @param \Twig\Environment $twig L'environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de fichiers Twig
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
        $this->contenuDAO = new ContenuDAO($this->getPdo());
    }

    /**
     * @brief Affiche les détails d'un film
     */
    public function afficherContenu(): void {
        $tmdbId = isset($_GET['tmdb_id']) ? intval($_GET['tmdb_id']) : null;

        if ($tmdbId) {
            // Récupérer les données du film via le DAO
            $contenu = $this->contenuDAO->getContentFromTMDB($tmdbId);

            if ($contenu) {
                // Récupérer les personnalités via le DAO
                $movieData = $this->contenuDAO->getMovieData($tmdbId);
                $personnalites = $this->contenuDAO->getPersonnalites($movieData);

                // Récupérer les thèmes via le DAO
                $themes = $this->contenuDAO->getGenres($movieData);

                // Récupérer les notes et commentaires
                $commentaireDAO = new CommentaireDAO($this->getPdo());
                $notes = $commentaireDAO->getMoyenneEtTotalNotesContenu($tmdbId);
                $commentaires = $commentaireDAO->getCommentairesContenu($tmdbId);

                // Récupérer les thèmes depuis la BD
                $themeDAO = new ThemeDAO($this->getPdo());
                $themesFromDB = [];

                foreach ($themes as $theme) {
                    $themeFromDB = $themeDAO->createIfNotExists($theme);
                    if ($themeFromDB) {
                        $themesFromDB[] = $themeFromDB;
                    }
                }

                // Récupérer les watchlists pour l'utilisateur connecté
                $watchlistDAO = new WatchlistDAO($this->getPdo());
                $watchlists = null;
                if (isset($_SESSION['utilisateur'])) {
                    $watchlists = $watchlistDAO->findByUser(unserialize($_SESSION['utilisateur'])->getId());
                }

                // Récupération des toasts
                $toastsWatchlist = null;
                if (isset($_SESSION['toastsWatchlist'])) {
                    $toastsWatchlist = $_SESSION['toastsWatchlist'];
                    unset($_SESSION['toastsWatchlist']);
                }

                // Afficher le template avec les données
                echo $this->getTwig()->render('pageDunContenu.html.twig', [
                    'contenu' => $contenu,
                    'personnalite' => $personnalites,
                    'themes' => $themesFromDB,
                    'moyenne' => $notes['moyenne'],
                    'total' => $notes['total'],
                    'commentaires' => $commentaires,
                    'watchlists' => $watchlists,
                    'toastsWatchlist' => $toastsWatchlist
                ]);
                return;
            }
        }
    }

} 