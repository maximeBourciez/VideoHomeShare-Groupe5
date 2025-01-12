<?php

class ControllerWatchlist extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function afficherWatchlists(): void {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }
    
        $userId = $_SESSION['user_id'];
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        // Récupérer les watchlists de l'utilisateur
        $watchlistsPerso = $watchlistDAO->findByUser($userId);
    
        // Pour chaque watchlist, récupérer ses contenus
        foreach ($watchlistsPerso as $watchlist) {
            $contenus = $watchlistDAO->getWatchlistContent($watchlist->getId());
            $watchlist->setContenus($contenus);
        }
    
        // Afficher le template avec les données
        echo $this->getTwig()->render('watchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso,
            'favoris' => $this->getFavoris($userId) // Assure-toi de récupérer les favoris de l'utilisateur
        ]);
    }

    public function ajouterAWatchlist(): void {
        if (!isset($_SESSION['user_id']) || !isset($_POST['watchlistId']) || !isset($_POST['contenuId'])) {
            header('HTTP/1.1 400 Bad Request');
            return;
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $watchlistDAO->addContenuToWatchlist(
            intval($_POST['watchlistId']),
            intval($_POST['contenuId'])
        );

        header('Location: /watchlists');
    }

    public function creerWatchlist(): void {
        if (!isset($_SESSION['user_id']) || !isset($_POST['nom'])) {
            header('Location: /watchlists');
            return;
        }

        $watchlist = new Watchlist(
            null,
            $_POST['nom'],
            $_POST['description'] ?? '',
            isset($_POST['estPublique']),
            null,
            $_SESSION['user_id']
        );

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $watchlistDAO->createWatchlist($watchlist);

        header('Location: /watchlists');
    }
}
