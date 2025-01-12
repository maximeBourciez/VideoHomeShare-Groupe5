<?php

class  ControllerWhatchlist extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function afficherWatchlists(): void {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $watchlistDAO = new WatchlistDAO($this->getPdo());
              
        // Pour chaque watchlist, récupérer ses contenus
        foreach ($watchlistsPerso as $watchlist) {
            $contenus = $watchlistDAO->getWatchlistContent($watchlist->getId());
            $watchlist->setContenus($contenus);
        }

        // Afficher le template avec les données
        echo $this->getTwig()->render('watchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso,
        ]);
    }

    public function ajouterAWatchlist(): void {
        if (!isset($_SESSION['user_id']) || !isset($_POST['watchlistId']) || !isset($_POST['contenuId'])) {
            header('HTTP/1.1 400 Bad Request');
            return;
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $success = $watchlistDAO->addContenuToWatchlist(
            intval($_POST['watchlistId']),
            intval($_POST['contenuId'])
        );

        header('Content-Type: application/json');
        echo json_encode(['success' => $success]);
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