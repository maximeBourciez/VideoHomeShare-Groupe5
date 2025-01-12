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
        $tmdbApi = new ContenuDAO($this->getPdo());
        $trends = $tmdbApi->getTrendingMovies();

        // Transformation du tableau
        $tendances = $this->addNotesToContenus($trends);

        // Récupérer les films d'action (genre ID 28)
        $actionMovies = $tmdbApi->getPopularMoviesByGenre(28, 20);
        $actionMovies = $this->addNotesToContenus($actionMovies);

        // Récupérer les films d'aventure (genre ID 12)
        $adventureMovies = $tmdbApi->getPopularMoviesByGenre(12, 20);
        $adventureMovies = $this->addNotesToContenus($adventureMovies);

        // Récupérer les 3 fils les plus likés de la semaine
        $filDAO = new FilDAO($this->getPdo());
        $fils = $filDAO->getFilsLesPlusLikes(3);

        // Afficher le template avec les données
        echo $this->getTwig()->render('index.html.twig', [
            'trends' => $tendances,
            'actionMovies' => $actionMovies,
            'adventureMovies' => $adventureMovies,
            'fils' => $fils,
            'test' => $tendances
        ]);
    }

    /**
     * @brief Méthode permettant de modifier un tableau de contenus pour lui ajouter les infos pour les notes
     * 
     * @param array<Contenu> $contenus Tableau de contenus
     * 
     * @return array<Contenu> Tableau de contenus avec les infos pour les notes 
     */
    private function addNotesToContenus(array $contenus): array{
        $commentaire = new CommentaireDAO($this->getPdo());
        $tendances = [];
        for($i = 1; $i < count($contenus); $i++) {
            // Ajouter une clé moyenne et nbAvis à chaque film
            $tendances[$i][0] = $contenus[$i];
            $tendances[$i][1]["moyenne"] = $commentaire->getMoyenneEtTotalNotesContenu($contenus[$i]->getId())["moyenne"];
            $tendances[$i][1]["nbAvis"] = $commentaire->getMoyenneEtTotalNotesContenu($contenus[$i]->getId())["total"];
        }
        return $tendances;
    }


    /**
     * @brief Méthode permettant la recherche de contenu
     * 
     * @details méthode recherchant, films, séries, saga, fils et quizz par rapport à une recherche
     * 
     * @return void
     */
    public function rechercher(){
        // Récupérer le terme de recherche
        $recherche = $_POST['recherche'];

        // Récupérer les contenus
        $managerContenu = new ContenuDAO($this->getPdo());
        $contenus = $managerContenu->searchMoviesByName($recherche);

        // Récupérer les collections
        $managerCollection = new CollectionDAO($this->getPdo());
        $collections = $managerCollection->searchByName($recherche);

        // Récupérer les sagas
        // En attente du travail sur les sagas

        // Récupérer les threads (fils)
        $filDAO = new FilDAO($this->getPdo());
        $fils = $filDAO->searchFils($recherche);

        // Rendre la vue
        echo $this->getTwig()->render('resultatRecherche.html.twig', [
            'contenus' => $contenus,
            'fils' => $fils,
            'collections' => $collections,
            'recherche' => $recherche
        ]);
    }
}