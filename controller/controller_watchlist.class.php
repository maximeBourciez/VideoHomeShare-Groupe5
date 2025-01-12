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
    
        $userId = unserialize($_SESSION['utilisateur'])->getId();
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        // Récupérer les watchlists de l'utilisateur
        $watchlistsPerso = $watchlistDAO->findByUser($userId);
    
        // Pour chaque watchlist, récupérer ses contenus
        foreach ($watchlistsPerso as $watchlist) {
            $contenus = $watchlistDAO->getWatchlistContent($watchlist->getId());
            $watchlist->setContenus($contenus);
        }
    
        // Afficher le template avec les données
        echo $this->getTwig()->render('whatchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso,
        ]);
    }

    public function ajouterAWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['watchlistId']) || !isset($_POST['contenuId'])) {
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
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['nom'])) {
            // Remplacer header() par le gestionnaire de connexion
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        $nom = $_POST['nom'];
        $description = $_POST['description'] ?? '';
        $estPublique = isset($_POST['estPublique']);
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $idWatchlist = $watchlistDAO->create($nom, $description, $estPublique, $idUtilisateur);
    
        // Récupérer les watchlists mises à jour pour l'affichage
        $watchlistsPerso = $watchlistDAO->findByUser($idUtilisateur);
    
        // Rediriger vers la page des watchlists avec les données mises à jour
        echo $this->getTwig()->render('watchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso
        ]);
    }
}
