<?php

/**
 * @brief Contrôleur gérant l'affichage des séries
 *
 * Cette classe gère l'affichage des informations détaillées des séries TV
 * en utilisant le DAO pour accéder aux données
 *
 * @author François Barlic <<francois.barlic57@gmail.com>>
 * @version 1.0
 */
class ControllerSerie extends Controller
{
    /** @var SerieDAO Instance d'accès aux données des séries */
    private SerieDAO $serieDAO;

    /**
     * @brief Constructeur du contrôleur de série
     *
     * @param \Twig\Environment $twig L'environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Le chargeur de fichiers Twig
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
        $this->serieDAO = new SerieDAO($this->getPdo());
    }

    /**
     * @brief Affiche les détails d'une série
     *
     * Cette méthode :
     * - Récupère l'ID TMDB de la série depuis l'URL
     * - Utilise le DAO pour obtenir les informations de la série
     * - Récupère les saisons de la série
     * - Récupère les personnalités de la série
     * - Récupère les notes et commentaires existants
     * - Affiche le tout dans un template Twig
     *
     * Si la série n'est pas trouvée ou si l'ID n'est pas fourni,
     * redirige vers la page d'accueil
     *
     * @return void
     */
    public function afficherSerie(): void
    {
        $tmdbId = isset($_GET['tmdb_id']) ? intval($_GET['tmdb_id']) : null;

        if ($tmdbId) {
            // Récupérer les données de la série via le DAO
            $serie = $this->serieDAO->getSerieFromTMDB($tmdbId);

            if ($serie) {
                // Récupérer les saisons de la série
                $saisons = $this->serieDAO->getSeasonsFromSerie($tmdbId);
                // Récupérer les personnalités de la série
                $personnalites = $this->serieDAO->getPersonnalitesSerie($tmdbId);
                // Récupérer les notes et commentaires
                $commentaireDAO = new CommentaireDAO($this->getPdo());
                $notes = $commentaireDAO->getMoyenneEtTotalNotesSerie($tmdbId);
                $commentaires = $commentaireDAO->getCommentairesSerie($tmdbId);

                // Afficher le template avec les données
                echo $this->getTwig()->render('pageDuneSerie.html.twig', [
                    'serie' => $serie,
                    'personnalites' => $personnalites,
                    'saisons' => $saisons,
                    'moyenne' => $notes['moyenne'],
                    'total' => $notes['total'],
                    'commentaires' => $commentaires
                ]);
                return;
            }
        }
        // Si pas d'ID ou série non trouvée, rediriger vers la page d'accueil
        echo $this->getTwig()->render('index.html.twig');
    }
}
