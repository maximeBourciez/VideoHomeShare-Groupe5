<?php

/**
 * @brief Classe ControllerIndex
 * 
 * @details Classe permettant de gérer le controller de la page d'accueil
 * 
 * @date 5/11/2024
 */
class ControllerIndex extends Controller
{
    /**
     * @brief Méthode permettant d'afficher la page d'accueil
     *
     * @return void
     */
    public function index()
    {
        // Récupérer les films de la semaine
        $tmdbApi = new TmdbAPIContenu(TMDB_API_KEY);
        $trends = $tmdbApi->getTrendingMovies();

        // Récupérer les notes et le nombre de notes de chaque film
        // $commentaireDAO = new CommentaireDAO($this->getPdo());
        // foreach($trends as $trend) {
        //     $notes = $commentaireDAO->getMoyenneEtTotalNotesContenu($trend->getId());
        //     $trend->setMoyenne($notes['moyenne']);
        //     $trend->setTotal($notes['total']);
        // }

        // Récupérer les films d'action (genre ID 28)
        $actionMovies = $tmdbApi->getPopularMoviesByGenre(28, 20);

        // Récupérer les films d'aventure (genre ID 12)
        $adventureMovies = $tmdbApi->getPopularMoviesByGenre(12, 20);

        // Récupérer les 3 fils les plus likés de la semaine
        $filDAO = new FilDAO($this->getPdo());
        // $fils = $filDAO->getFilsLesPlusLikes(3);

        // Afficher le template avec les données
        echo $this->getTwig()->render('index.html.twig', [
            'trends' => $trends,
            'actionMovies' => $actionMovies,
            'adventureMovies' => $adventureMovies,
            //'fils' => $fils
        ]);
    }
}