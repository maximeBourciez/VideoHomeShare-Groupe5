<?php

/**
 * @brief Contrôleur gérant l'affichage des collections
 * 
 * Cette classe gère l'affichage des informations détaillées des collections de films
 * en utilisant le DAO pour accéder aux données
 * 
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class ControllerCollection extends Controller {
    /** @var CollectionDAO Instance d'accès aux données des collections */
    private CollectionDAO $collectionDAO;

    /**
     * @brief Constructeur du contrôleur de collection
     * 
     * @param \Twig\Environment $twig L'environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de fichiers Twig
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
        $this->collectionDAO = new CollectionDAO($this->getPdo());
    }

    /**
     * @brief Affiche les détails d'une collection
     * 
     * Cette méthode :
     * - Récupère l'ID TMDB de la collection depuis l'URL
     * - Utilise le DAO pour obtenir les informations de la collection
     * - Récupère les films de la collection
     * - Récupère les notes et commentaires existants
     * - Affiche le tout dans un template Twig
     * 
     * Si la collection n'est pas trouvée ou si l'ID n'est pas fourni,
     * redirige vers la page d'accueil
     * 
     * @return void
     */
    public function afficherCollection(): void {
        $tmdbId = isset($_GET['tmdb_id']) ? intval($_GET['tmdb_id']) : null;
        
        if ($tmdbId) {
            // Récupérer les données de la collection via le DAO
            $collection = $this->collectionDAO->getCollectionFromTMDB($tmdbId);
            
            if ($collection) {
                // Récupérer les films de la collection
                $films = $this->collectionDAO->getMoviesFromCollection($tmdbId);

                // Récupérer les personnalités de la collection
                $personnalites = $this->collectionDAO->getPersonnalitesCollection($tmdbId);

                // Récupérer les notes et commentaires
                $commentaireDAO = new CommentaireDAO($this->getPdo());
                $notes = $commentaireDAO->getMoyenneEtTotalNotesCollection($tmdbId);
                $commentaires = $commentaireDAO->getCommentairesCollection($tmdbId);
                
                // Afficher le template avec les données
                echo $this->getTwig()->render('pageDuneCollection.html.twig', [
                    'collection' => $collection,
                    'personnalites' => $personnalites,
                    'films' => $films,
                    'moyenne' => $notes['moyenne'],
                    'total' => $notes['total'],
                    'commentaires' => $commentaires
                ]);
                return;
            }
        }

        // Si pas d'ID ou collection non trouvée, rediriger vers la page d'accueil
        echo $this->getTwig()->render('index.html.twig');
    }
} 