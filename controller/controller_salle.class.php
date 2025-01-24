<?php


class ControllerSalle extends Controller
{
    /**
     * @brief Constructeur de la classe ControllerSignalement
     * 
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Affiche l'acceil du watch2gether
     * @return void
     */
    public function accueilWatch2Gether() : void
    {
        $managersalle = new SalleDAO($this->getPdo());
        
        $salles = $managersalle->findAllPubliqueDisponible();
        $template = $this->getTwig()->load('acceilWatch2Gether.html.twig');
        echo $template->render(array( 'salles' => $salles));
    }


    public function afficherSalle(){
        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;

        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->find($id);
        $salle->setPlaceDisponible($salle->getPlaceDisponible()-1);
        if($salle->getPlaceDisponible() < 0){
            $this->accueilWatch2Gether();
            exit();
        }
        $managersalle->update($salle);
        $template = $this->getTwig()->load('visionageW2G.html.twig');
        echo $template->render(array('salle' => $salle));


    }
    public function rejoindreSallePriver(){
        $code = isset($_POST['code']) ?  htmlspecialchars($_POST['code']) : null;
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        $hote = false;
        if (isset($_SESSION['salle'])) {
            if ($_SESSION['salle'][$code] == "Hote") {
                $hote = true;
            }
        }
        
        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->findByCode($code);
        $salle->setPlaceDisponible($salle->getPlaceDisponible()-1);
        if($salle->getPlaceDisponible() < 0){
            $this->accueilWatch2Gether();
            exit();
        }
        $managersalle->update($salle);
        $template = $this->getTwig()->load('visionageW2G.html.twig');
        echo $template->render(array('salle' => $salle,'Hote' => $hote));

    }

    public function createSalle(){

        // verifier si l'utilisateur est connecter
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }

        // recuperer les informations du formulaire
        $nom = isset($_POST['nom']) ?  htmlspecialchars($_POST['nom']) : null;
        $nbpersonne = isset($_POST['capaciter']) ?  htmlspecialchars($_POST['capaciter']) : null;
        $Publique = isset($_POST['Publique']) ?  true : false;
        $genre = isset($_POST['genre']) ?  htmlspecialchars($_POST['genre']) : null;
        // creer la salle
        $managersalle = new SalleDAO($this->getPdo());
        $salle = new Salle();
        $salle->setNom($nom);
        $salle->setNbPersonne($nbpersonne);
        $salle->setPlaceDisponible($nbpersonne);  
        $salle->setEstPublique($Publique);
        $salle->setGenre($genre);
        if(!$Publique){
        $salle->setCode(random_int(0,9999));  
        while( !$managersalle->create($salle)){
            $salle->setCode(random_int(0,9999));
        }
        }else{
            $managersalle->create($salle);
        }
        // recuperer l'id de la salle
        $lastInsertId = $this->getPdo()->lastInsertId();
        $salle = $managersalle->find($lastInsertId);
        // ajouter l'utilisateur comme hote
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $managersalle->ajouterRole($utilisateur->getId(),$salle->getIdSalle(),'Hote');
        $_SESSION['salle'] = array([$salle->getIdSalle() => "Hote"]);
        // afficher la salle
        $template = $this->getTwig()->load('visionageW2G.html.twig');
        echo $template->render(array('salle' => $salle,'Hote' => true));
    }


    public function quiter(){

        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->find($id);
        $salle->setPlaceDisponible($salle->getPlaceDisponible()+1);
        $managersalle->update($salle);

    }

    

}