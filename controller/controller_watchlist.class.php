<?php

class ControllerWhatchlist  extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    public function afficherWatchlists(): void {
        if (!isset($_SESSION['utilisateur'])) {
            // Remplacer header() par le gestionnaire de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $userId = $_SESSION['utilisateur']->getId();
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
            // Remplacer header() par le gestionnaire de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $watchlistDAO->addContenuToWatchlist(
            intval($_POST['watchlistId']),
            intval($_POST['contenuId'])
        );

        // Rediriger vers la page des watchlists
        echo $this->getTwig()->render('watchlists.html.twig');
    }

    public function creerWatchlist(): void {
        if (!isset($_SESSION['user_id']) || !isset($_POST['nom'])) {
            // Remplacer header() par le gestionnaire de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
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

        // Rediriger vers la page des watchlists
        echo $this->getTwig()->render('watchlists.html.twig');
    }
}
