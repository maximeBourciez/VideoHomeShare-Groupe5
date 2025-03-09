<?php
/**
 * @brief Contrôleur gérant les watchlistss
 */
class ControllerWatchlist extends Controller {
    /**
     * @brief Constructeur du contrôleur
     * @param \Twig\Environment $twig Instance de Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de fichiers Twig
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader) {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche les watchlists de l'utilisateur connecté
     */
    public function afficherWatchlists(): void {
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $userId = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        $watchlistsPerso = $watchlistDAO->findByUser($userId);
    
        foreach ($watchlistsPerso as $watchlist) {
            $contenus = $watchlistDAO->getWatchlistContent($watchlist->getId());
            $watchlist->setContenus($contenus);
        }
    
        echo $this->getTwig()->render('watchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso,
        ]);
    }

    /**
     * @brief Ajoute un contenu à une watchlist
     */
    public function ajouterAWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['watchlists']) || !isset($_POST['idContenu'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        $contenuId = filter_var($_POST['idContenu'], FILTER_VALIDATE_INT);
        if ($contenuId === false) {
            throw new Exception("ID de contenu invalide");
        }
    
        $watchlists = array_map('intval', (array)$_POST['watchlists']); // Convertit les valeurs en entiers dans un tableau
    
        foreach ($watchlists as $watchlistId) {
            if (!$watchlistDAO->isContenuInWatchlist($watchlistId, $contenuId)) { // Vérifie pour chaque watchlist individuellement
                $watchlistDAO->addContenuToWatchlist($watchlistId, $contenuId);
            }
        }
    
        $this->afficherWatchlists();
    }

    /**
     * @brief Ajoute une collection à une watchlist
     */
    public function ajouterCollectionAWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['watchlists']) || !isset($_POST['idCollection'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        $collectionId = filter_var($_POST['idCollection'], FILTER_VALIDATE_INT);
        if ($collectionId === false) {
            throw new Exception("ID de collection invalide");
        }
    
        $watchlists = array_map('intval', (array)$_POST['watchlists']); // Convertit les valeurs en entiers dans un tableau

        foreach ($watchlists as $watchlistId) {
            if (!$watchlistDAO->isCollectionInWatchlist($watchlistId, $collectionId)) { // Vérifie pour chaque watchlist individuellement
                $watchlistDAO->addCollectionToWatchlist($watchlistId, $collectionId);
            }
        }
        
        $this->afficherWatchlists();
    }

    /**
     * @brief Ajoute une série à une watchlist
     */
    public function ajouterSerieAWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['watchlists']) || !isset($_POST['idSerie'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        $serieId = filter_var($_POST['idSerie'], FILTER_VALIDATE_INT);
        if ($serieId === false) {
            throw new Exception("ID de série invalide");
        }
    
        $watchlists = array_map('intval', (array)$_POST['watchlists']); // Convertit les valeurs en entiers dans un tableau

        foreach ($watchlists as $watchlistId) {
            if (!$watchlistDAO->isSerieInWatchlist($watchlistId, $serieId)) { // Vérifie pour chaque watchlist individuellement
                $watchlistDAO->addSerieToWatchlist($watchlistId, $serieId);
            }
        }
        
        $this->afficherWatchlists();
    }

    /**
     * @brief Crée une nouvelle watchlist
     */
    public function creerWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['nom'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $idUtilisateur = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $nom = htmlspecialchars(trim($_POST['nom']));
        $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
        $estPublique = isset($_POST['estPublique']) ? (bool)1 : (bool)0;
    
        if (empty($nom)) {
            throw new Exception("Le nom de la watchlist est requis");
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $idWatchlist = $watchlistDAO->create($nom, $description, $estPublique, $idUtilisateur);
    
        if (!$idWatchlist) {
            throw new Exception("Erreur lors de la création de la watchlist");
        }
    
        $this->afficherWatchlists();
    }

    /**
     * @brief Modifie une watchlist existante
     */
    public function modifierWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['id']) || !isset($_POST['nom'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
    
        $idUtilisateur = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $idWatchlist = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($idWatchlist === false) {
            throw new Exception("ID de watchlist invalide");
        }
        
        $nom = htmlspecialchars(trim($_POST['nom']));
        $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
        $estPublique = isset($_POST['estPublique']) ? (bool)1 : (bool)0;
    
        if (empty($nom)) {
            throw new Exception("Le nom de la watchlist est requis");
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        // Vérification des droits d'accès
        $watchlists = $watchlistDAO->findByUser($idUtilisateur);
        $watchlistAppartientUtilisateur = false;
        
        foreach ($watchlists as $watchlist) {
            if ($watchlist->getId() === $idWatchlist) {
                $watchlistAppartientUtilisateur = true;
                break;
            }
        }
    
        if (!$watchlistAppartientUtilisateur) {
            throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
        }
    
        $success = $watchlistDAO->update($idWatchlist, $nom, $description, $estPublique);
        
        if (!$success) {
            throw new Exception("Erreur lors de la modification de la watchlist");
        }
    
        $this->afficherWatchlists();
    }

    /**
     * @brief Supprime une watchlist existante
     */
    public function supprimerWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['id'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }

        $idUtilisateur = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $idWatchlist = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        if ($idWatchlist === false) {
            throw new Exception("ID de watchlist invalide");
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        // Vérification des droits d'accès
        $watchlists = $watchlistDAO->findByUser($idUtilisateur);
        $watchlistAppartientUtilisateur = false;
        
        foreach ($watchlists as $watchlist) {
            if ($watchlist->getId() === $idWatchlist) {
                $watchlistAppartientUtilisateur = true;
                break;
            }
        }

        if (!$watchlistAppartientUtilisateur) {
            throw new Exception("Vous n'avez pas les droits pour supprimer cette watchlist");
        }

        $success = $watchlistDAO->delete($idWatchlist);
        
        if (!$success) {
            throw new Exception("Erreur lors de la suppression de la watchlist");
        }

        $this->afficherWatchlists();
    }

    /**
     * @brief Supprime un contenu d'une watchlist
     */
    public function supprimerDeWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['idWatchlist']) || !isset($_POST['idContenu'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }

        $idUtilisateur = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $idWatchlist = filter_var($_POST['idWatchlist'], FILTER_VALIDATE_INT);
        $idContenu = filter_var($_POST['idContenu'], FILTER_VALIDATE_INT);
        
        if ($idWatchlist === false || $idContenu === false) {
            throw new Exception("ID de watchlist ou de contenu invalide");
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        // Vérification des droits d'accès
        $watchlists = $watchlistDAO->findByUser($idUtilisateur);
        $watchlistAppartientUtilisateur = false;
        
        foreach ($watchlists as $watchlist) {
            if ($watchlist->getId() === $idWatchlist) {
                $watchlistAppartientUtilisateur = true;
                break;
            }
        }

        if (!$watchlistAppartientUtilisateur) {
            throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
        }

        $success = $watchlistDAO->removeContenuFromWatchlist($idWatchlist, $idContenu);
        
        if (!$success) {
            throw new Exception("Erreur lors de la suppression du contenu de la watchlist");
        }

        $this->afficherWatchlists();
    }

    /**
     * @brief Supprime une collection de la watchlist
     */
    public function supprimerCollectionDeWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['idWatchlist']) || !isset($_POST['idCollection'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }

        $idUtilisateur = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $idWatchlist = filter_var($_POST['idWatchlist'], FILTER_VALIDATE_INT);
        $idCollection = filter_var($_POST['idCollection'], FILTER_VALIDATE_INT);
        
        if ($idWatchlist === false || $idCollection === false) {
            throw new Exception("ID de watchlist ou de collection invalide");
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        // Vérification des droits d'accès
        $watchlists = $watchlistDAO->findByUser($idUtilisateur);
        $watchlistAppartientUtilisateur = false;
        
        foreach ($watchlists as $watchlist) {
            if ($watchlist->getId() === $idWatchlist) {
                $watchlistAppartientUtilisateur = true;
                break;
            }
        }

        if (!$watchlistAppartientUtilisateur) {
            throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
        }

        $success = $watchlistDAO->removeCollectionFromWatchlist($idWatchlist, $idCollection);
        
        if (!$success) {
            throw new Exception("Erreur lors de la suppression de la collection de la watchlist");
        }

        $this->afficherWatchlists();
    }

    /**
     * @brief Supprime une serie de la watchlist
     */
    public function supprimerSerieDeWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['idWatchlist']) || !isset($_POST['idSerie'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }

        $idUtilisateur = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $idWatchlist = filter_var($_POST['idWatchlist'], FILTER_VALIDATE_INT);
        $idSerie = filter_var($_POST['idSerie'], FILTER_VALIDATE_INT);
        
        if ($idWatchlist === false || $idSerie === false) {
            throw new Exception("ID de watchlist ou de série invalide");
        }

        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        // Vérification des droits d'accès
        $watchlists = $watchlistDAO->findByUser($idUtilisateur);
        $watchlistAppartientUtilisateur = false;
        
        foreach ($watchlists as $watchlist) {
            if ($watchlist->getId() === $idWatchlist) {
                $watchlistAppartientUtilisateur = true;
                break;
            }
        }

        if (!$watchlistAppartientUtilisateur) {
            throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
        }

        $success = $watchlistDAO->removeSerieFromWatchlist($idWatchlist, $idSerie);
        
        if (!$success) {
            throw new Exception("Erreur lors de la suppression de la série de la watchlist");
        }

        $this->afficherWatchlists();
    }



}