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

            $partages = $watchlistDAO->getWatchlistPartages($watchlist->getId());
            $watchlist->setPartages($partages);
        }

        $watchlistsPartagees = $watchlistDAO->getWatchlistsSharedWithUser($userId);
    
        echo $this->getTwig()->render('watchlists.html.twig', [
            'watchlistsPerso' => $watchlistsPerso,
            'watchlistsPartagees' => $watchlistsPartagees,
            'utilisateur' => unserialize($_SESSION['utilisateur'])
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
    
        if (!isset($_SESSION['toastsWatclist'])) {
            $toastsWatchlist = [];
        }
        else {
            $toastsWatchlist = $_SESSION['toastsWatchlist'];
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        $contenuId = filter_var($_POST['idContenu'], FILTER_VALIDATE_INT);

        if ($contenuId === false) {

            array_push($toastsWatchlist, [
                'indiqueSuccessWatchlist' => false,
                'messageInfosWatchlist' => "Erreur : le contenu n'existe pas."
            ]);
        }
        else {
            $watchlists = array_map('intval', (array)$_POST['watchlists']); // Convertit les valeurs en entiers dans un tableau
    
            foreach ($watchlists as $watchlistId) {

                if (!$watchlistDAO->isContenuInWatchlist($watchlistId, $contenuId)) { // Vérifie pour chaque watchlist individuellement
                    $watchlistDAO->addContenuToWatchlist($watchlistId, $contenuId);
                    array_push($toastsWatchlist, [
                        'indiqueSuccessWatchlist' => true,
                        'messageInfosWatchlist' => "Le contenu a bien été ajoutée à " . $watchlistDAO->findById($watchlistId)->getNom() . "."
                    ]);
                }
                else {
                    array_push($toastsWatchlist, [
                        'indiqueSuccessWatchlist' => false,
                        'messageInfosWatchlist' => "Le contenu est déjà dans " . $watchlistDAO->findById($watchlistId)->getNom() . "."
                    ]);
                }
            }
        }

        $_SESSION['toastsWatchlist'] = $toastsWatchlist;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
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

        if (!isset($_SESSION['toastsWatclist'])) {
            $toastsWatchlist = [];
        }
        else {
            $toastsWatchlist = $_SESSION['toastsWatchlist'];
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        
        $collectionId = filter_var($_POST['idCollection'], FILTER_VALIDATE_INT);

        if ($collectionId === false) {

            array_push($toastsWatchlist, [
                'indiqueSuccessWatchlist' => false,
                'messageInfosWatchlist' => "Erreur : la collection n'existe pas."
            ]);
        }
        else {

            $watchlists = array_map('intval', (array)$_POST['watchlists']); // Convertit les valeurs en entiers dans un tableau

            foreach ($watchlists as $watchlistId) {

                if (!$watchlistDAO->isCollectionInWatchlist($watchlistId, $collectionId)) { // Vérifie pour chaque watchlist individuellement
                    $watchlistDAO->addCollectionToWatchlist($watchlistId, $collectionId);
                    array_push($toastsWatchlist, [
                        'indiqueSuccessWatchlist' => true,
                        'messageInfosWatchlist' => "La collection a bien été ajoutée à " . $watchlistDAO->findById($watchlistId)->getNom() . "."
                    ]);
                }
                else {
                    array_push($toastsWatchlist, [
                        'indiqueSuccessWatchlist' => false,
                        'messageInfosWatchlist' => "La collection est déjà dans " . $watchlistDAO->findById($watchlistId)->getNom() . "."
                    ]);
                }
            }
        }

        $_SESSION['toastsWatchlist'] = $toastsWatchlist;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
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

        if (!isset($_SESSION['toastsWatclist'])) {
            $toastsWatchlist = [];
        }
        else {
            $toastsWatchlist = $_SESSION['toastsWatchlist'];
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
    
        $serieId = filter_var($_POST['idSerie'], FILTER_VALIDATE_INT);

        if ($serieId === false) {

            array_push($toastsWatchlist, [
                'indiqueSuccessWatchlist' => false,
                'messageInfosWatchlist' => "Erreur : la série n'existe pas."
            ]);
        }
        else {

            $watchlists = array_map('intval', (array)$_POST['watchlists']); // Convertit les valeurs en entiers dans un tableau

            foreach ($watchlists as $watchlistId) {

                if (!$watchlistDAO->isSerieInWatchlist($watchlistId, $serieId)) { // Vérifie pour chaque watchlist individuellement
                    $watchlistDAO->addSerieToWatchlist($watchlistId, $serieId);
                    array_push($toastsWatchlist, [
                        'indiqueSuccessWatchlist' => true,
                        'messageInfosWatchlist' => "La série a bien été ajoutée à " . $watchlistDAO->findById($watchlistId)->getNom() . "."
                    ]);
                }
                else {
                    array_push($toastsWatchlist, [
                        'indiqueSuccessWatchlist' => false,
                        'messageInfosWatchlist' => "La série est déjà dans " . $watchlistDAO->findById($watchlistId)->getNom() . "."
                    ]);
                }
            }
        }
    
        $_SESSION['toastsWatchlist'] = $toastsWatchlist;
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();

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
            // throw new Exception("Le nom de la watchlist est requis");
            $this->afficherWatchlists();
        }
    
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $idWatchlist = $watchlistDAO->create($nom, $description, $estPublique, $idUtilisateur);
    
        if (!$idWatchlist) {
            // throw new Exception("Erreur lors de la création de la watchlist");
            $this->afficherWatchlists();
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
            // throw new Exception("ID de watchlist invalide");
            $this->afficherWatchlists();
        }
        
        $nom = htmlspecialchars(trim($_POST['nom']));
        $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : '';
        $estPublique = isset($_POST['estPublique']) ? (bool)1 : (bool)0;
    
        if (empty($nom)) {
            // throw new Exception("Le nom de la watchlist est requis");
            $this->afficherWatchlists();
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
            // throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
            $this->afficherWatchlists();
        }
    
        $success = $watchlistDAO->update($idWatchlist, $nom, $description, $estPublique);
        
        if (!$success) {
            // throw new Exception("Erreur lors de la modification de la watchlist");
            $this->afficherWatchlists();
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
            // throw new Exception("ID de watchlist invalide");
            $this->afficherWatchlists();
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
            // throw new Exception("Vous n'avez pas les droits pour supprimer cette watchlist");
            $this->afficherWatchlists();
        }

        $success = $watchlistDAO->delete($idWatchlist);
        
        if (!$success) {
            // throw new Exception("Erreur lors de la suppression de la watchlist");
            $this->afficherWatchlists();
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
            // throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
            $this->afficherWatchlists();
        }

        $success = $watchlistDAO->removeContenuFromWatchlist($idWatchlist, $idContenu);
        
        if (!$success) {
            // throw new Exception("Erreur lors de la suppression du contenu de la watchlist");
            $this->afficherWatchlists();
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
            // throw new Exception("ID de watchlist ou de collection invalide");
            $this->afficherWatchlists();
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
            // throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
            $this->afficherWatchlists();
        }

        $success = $watchlistDAO->removeCollectionFromWatchlist($idWatchlist, $idCollection);
        
        if (!$success) {
            // throw new Exception("Erreur lors de la suppression de la collection de la watchlist");
            $this->afficherWatchlists();
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
            // throw new Exception("ID de watchlist ou de série invalide");
            $this->afficherWatchlists();
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
            // throw new Exception("Vous n'avez pas les droits pour modifier cette watchlist");
            $this->afficherWatchlists();
        }

        $success = $watchlistDAO->removeSerieFromWatchlist($idWatchlist, $idSerie);
        
        if (!$success) {
            // throw new Exception("Erreur lors de la suppression de la série de la watchlist");
            $this->afficherWatchlists();
        }

        $this->afficherWatchlists();
    }

    /**
     * @brief Partager une watchlist
     */
    public function partagerWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['idWatchlist']) || !isset($_POST['idUtilisateurP'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
        $idWatchlist = htmlspecialchars($_POST['idWatchlist']);
        $idUtilisateurC = htmlspecialchars(unserialize($_SESSION['utilisateur'])->getId());
        $idUtilisateurP = htmlspecialchars($_POST['idUtilisateurP']);
        if ($idUtilisateurC === $idUtilisateurP) {
            $this->afficherWatchlists();
            return;
        }
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $utilisateurDAO = new UtilisateurDAO($this->getPdo());

        if ($utilisateurDAO->exist($idUtilisateurP)) {
            if (!$watchlistDAO->isWatchlistShared($idWatchlist, $idUtilisateurP)) {
                $watchlistDAO->partagerWatchlist($idWatchlist, $idUtilisateurC, $idUtilisateurP);
            }
        }

        $this->afficherWatchlists();
        return;
    }

    /**
     * @brief Arrêter de partager une watchlist
     */
    public function arreterDePartagerWatchlist(): void {
        if (!isset($_SESSION['utilisateur']) || !isset($_POST['idWatchlist']) || !isset($_POST['idUtilisateurP'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            return;
        }
        $idWatchlist = htmlspecialchars($_POST['idWatchlist']);
        $idUtilisateurP = htmlspecialchars($_POST['idUtilisateurP']);
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $watchlistDAO->retirerPartageWatchlist($idWatchlist, $idUtilisateurP);
        $this->afficherWatchlists();
        return;
    }

    /**
     * @brief afficher les watchlists publiques
     */
    public function afficherWatchlistsPubliques(): void {
        $watchlistDAO = new WatchlistDAO($this->getPdo());
        $watchlists = $watchlistDAO->getPublicWatchlists();
        echo $this->getTwig()->render('watchlistsPubliques.html.twig', [
            'watchlists' => $watchlists
        ]);
    }

}