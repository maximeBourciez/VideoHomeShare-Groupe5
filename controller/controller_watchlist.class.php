<?php

class ControllerWatchlist  extends Controller {
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
        echo $this->getTwig()->render('watchlists.html.twig', [
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
    
        // Récupérer les watchlists mises à jour
        $watchlistsPerso = $watchlistDAO->findByUser($idUtilisateur);
    
        // S'assurer que le chemin du template est correct
        try {
            echo $this->getTwig()->render('watchlists.html.twig', [
                'watchlistsPerso' => $watchlistsPerso
            ]);
        } catch (\Twig\Error\LoaderError $e) {
            // Log l'erreur ou affichez un message d'erreur approprié
            echo "Erreur lors du chargement du template : " . $e->getMessage();
        }
    }

    // Méthode pour modifier une watchlist
    public function modifierWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['id']) || !isset($_POST['nom'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $idUtilisateur = unserialize($_SESSION['utilisateur'])->getId();
        $idWatchlist = intval($_POST['id']);
        $nom = $_POST['nom'];
        $description = $_POST['description'] ?? '';
        $estPublique = isset($_POST['estPublique']);
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        // Vérifier que la watchlist appartient bien à l'utilisateur
        $watchlists = $watchlistDAO->findByUser($idUtilisateur);
        $watchlistAppartientUtilisateur = false;
        
        foreach ($watchlists as $watchlist) {
            if ($watchlist->getId() === $idWatchlist) {
                $watchlistAppartientUtilisateur = true;
                break;
            }
        }
    
        if (!$watchlistAppartientUtilisateur) {
            // Rediriger avec un message d'erreur
            $watchlistsPerso = $watchlistDAO->findByUser($idUtilisateur);
            echo $this->getTwig()->render('watchlists.html.twig', [
                'watchlistsPerso' => $watchlistsPerso,
                'error' => 'Vous n\'avez pas les droits pour modifier cette watchlist'
            ]);
            return;
        }
    
        // Effectuer la modification
        $success = $watchlistDAO->update($idWatchlist, $nom, $description, $estPublique);
    
        // Récupérer les watchlists mises à jour
        $watchlistsPerso = $watchlistDAO->findByUser($idUtilisateur);
    
        // Ajouter un message de succès ou d'erreur
        echo $this->getTwig()->render('watchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso,
            'message' => $success ? 'Watchlist modifiée avec succès' : 'Erreur lors de la modification'
        ]);
    }
}
