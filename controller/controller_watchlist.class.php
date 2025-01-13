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
        // Vérifier que l'utilisateur est connecté et que les données nécessaires sont présentes
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['watchlists']) || !isset($_POST['idContenu'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $contenuId = intval($_POST['idContenu']);
        $watchlists = $_POST['watchlists']; // Array contenant les ID des watchlists sélectionnées
    
        // Parcourir les watchlists sélectionnées et y ajouter le contenu
        foreach ($watchlists as $watchlistId) {
            $watchlistDAO->addContenuToWatchlist(intval($watchlistId), $contenuId);
        }
    
        // Rediriger vers la page des watchlists mises à jour
        $userId = unserialize($_SESSION['utilisateur'])->getId();
        $watchlistsPerso = $watchlistDAO->findByUser($userId);
    
        foreach ($watchlistsPerso as $watchlist) {
            $contenus = $watchlistDAO->getWatchlistContent($watchlist->getId());
            $watchlist->setContenus($contenus);
        }
    
        // Rediriger
        $managerUtilisateur = new ControllerWatchlist($this->getTwig(), $this->getLoader());
        $managerUtilisateur->afficherWatchlists();
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
            // Rediriger 
            $managerUtilisateur = new ControllerWatchlist($this->getTwig(), $this->getLoader());
            $managerUtilisateur->afficherWatchlists();
            return;
        }
    
        // Effectuer la modification
        $success = $watchlistDAO->update($idWatchlist, $nom, $description, $estPublique);

    
        // Rediriger
        $managerUtilisateur = new ControllerWatchlist($this->getTwig(), $this->getLoader());
        $managerUtilisateur->afficherWatchlists();
    }
}
