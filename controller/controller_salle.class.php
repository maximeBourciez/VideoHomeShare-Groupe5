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

        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->findByCode($code);
        $salle->setPlaceDisponible($salle->getPlaceDisponible()-1);
        if($salle->getPlaceDisponible() < 0){
            $this->accueilWatch2Gether();
            exit();
        }
        $managersalle->update($salle);
        $template = $this->getTwig()->load('visionageW2G.html.twig');
        echo $template->render(array('salle' => $salle));

    }

    public function createSalle(){
        $nom = isset($_POST['nom']) ?  htmlspecialchars($_POST['nom']) : null;
        $nbpersonne = isset($_POST['capaciter']) ?  htmlspecialchars($_POST['capaciter']) : null;
        $Publique = isset($_POST['Publique']) ?  true : false;
        $genre = isset($_POST['genre']) ?  htmlspecialchars($_POST['genre']) : null;
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
        $lastInsertId = $this->getPdo()->lastInsertId();
        $salle = $managersalle->find($lastInsertId);
        $template = $this->getTwig()->load('visionageW2G.html.twig');
        echo $template->render(array('salle' => $salle));
    }


    public function quiter(){

        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->find($id);
        $salle->setPlaceDisponible($salle->getPlaceDisponible()+1);
        $managersalle->update($salle);

    }
}