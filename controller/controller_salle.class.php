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
        $hote = $managersalle->findRole(unserialize($_SESSION['utilisateur'])->getId(),$salle->getIdSalle());
        $managersalle->update($salle);
        $template = $this->getTwig()->load('visionageW2G.html.twig');
        echo $template->render(array('salle' => $salle,'Hote' => $hote));


    }
    public function rejoindreSallePriver(){
        $code = isset($_POST['code']) ?  htmlspecialchars($_POST['code']) : null;
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        
        
        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->findByCode($code);
        $salle->setPlaceDisponible($salle->getPlaceDisponible()-1);
        if($salle->getPlaceDisponible() < 0){
            $this->accueilWatch2Gether();
            exit();
        }
        $hote = $managersalle->findRole(unserialize($_SESSION['utilisateur'])->getId(),$salle->getIdSalle());

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
        // creer le fichier json
        $data = array( 'video' => array('url' => null, 'etat' => null,'temps'=> null),'chat' => array('messages' => array()));
        file_put_contents("jsonW2G/salle$lastInsertId.json", json_encode($data));
        
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

    public function fermerSalle(){
        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $managersalle = new SalleDAO($this->getPdo());
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $role = $managersalle->findRole($utilisateur->getId(),$id);
        if( $role != "Hote"){
            $managerIndex = new ControllerIndex($this->getTwig(), $this->getLoader());
            $managerIndex->index();
            exit();
        }

        if (file_exists("jsonW2G/salle$id.json")) {
            // Supprimer le fichier
            unlink("jsonW2G/salle$id.json");
        }

        $managersalle->suprimersalle($id);
        $this->accueilWatch2Gether();

    }

    public function envoyerMessage(){

        // verifier si l'utilisateur est connecter
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        // ouvrir fichier json contenant les messages
        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $message = isset($_GET['message']) ?  htmlspecialchars($_GET['message']) : null;
        $jsonData = file_get_contents("jsonW2G/salle$id.json");

        if ($jsonData == false){
            $this->accueilWatch2Gether();
            exit();
        }
        $data = json_decode($jsonData, true);   
         

        $newmessage = array('id' => count($data['chat']['messages'])+1, 'message' => $message, 'auteur' => unserialize($_SESSION['utilisateur'])->getPseudo());
        $data['chat']['messages'][] = $newmessage;

        file_put_contents("jsonW2G/salle$id.json", json_encode($data));
    }


    public function majChat(){
        // verifier si l'utilisateur est connecter
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        // ouvrir fichier json contenant les messages
        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $jsonData = file_get_contents("jsonW2G/salle$id.json");

        if ($jsonData == false){
            $this->accueilWatch2Gether();
            exit();
        }   

        $data = json_decode($jsonData, true);

        echo json_encode($data);



    }


    public function majVideo(){
        // verifier si l'utilisateur est connecter
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }

        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $jsonData = file_get_contents("jsonW2G/salle$id.json");
        $data = json_decode($jsonData, true);
        
        if($data == null){
            $this->accueilWatch2Gether();
            exit();
        }

        echo json_encode($data);
    }

    public function envoyerinfoVideo(){
        // verifier si l'utilisateur est connecter
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }

        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $url = isset($_GET['url']) ?  htmlspecialchars($_GET['url']) : null;
        $etat = isset($_GET['etat']) ?  htmlspecialchars($_GET['etat']) : null;
        $temps = isset($_GET['temps']) ?  htmlspecialchars($_GET['temps']) : null;
        $jsonData = file_get_contents("jsonW2G/salle$id.json");
        $data = json_decode($jsonData, true);
        $url = str_replace("\\", "&", $url);
        $data['video']['url'] = $url;
        $data['video']['etat'] = $etat;
        $data['video']['temps'] = $temps;
        file_put_contents("jsonW2G/salle$id.json", json_encode($data));
        

    }

    public function prochainVideo(){
        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $managersalle = new SalleDAO($this->getPdo());
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $role = $managersalle->findRole($utilisateur->getId(),$id);
        $salle = $managersalle->find($id);
        if( $role != "Hote"){
            $managerIndex = new ControllerIndex($this->getTwig(), $this->getLoader());
            $managerIndex->index();
            exit();
        }

        if ( $salle->getRangCourant() == null){
            $salle->setRangCourant(1);
        }
        else{
            $salle->setRangCourant($salle->getRangCourant()+1);
        }
        $managersalle->update($salle);

        $url = $managersalle->getUrlVideo($salle->getIdSalle());
        if ($url == null){
            $salle->setRangCourant(1);
            $managersalle->update($salle);
            $url = $managersalle->getUrlVideo($salle->getIdSalle());
        }

        echo $url;

    }

    public function ajouterVideo(){
        
        
        $id = isset($_GET['id']) ?  htmlspecialchars($_GET['id']) : null;
        $url = isset($_GET['url']) ?  htmlspecialchars($_GET['url']) : null;

        $managersalle = new SalleDAO($this->getPdo());
        if (!isset($_SESSION['utilisateur'])) {
            $managerUtilisateur = new ControllerUtilisateur($this->getTwig(), $this->getLoader());
            $managerUtilisateur->connexion();
            exit();
        }
        $utilisateur = unserialize($_SESSION['utilisateur']);
        $role = $managersalle->findRole($utilisateur->getId(),$id);
        if( $role != "Hote"){
            $managerIndex = new ControllerIndex($this->getTwig(), $this->getLoader());
            $managerIndex->index();
            exit();
        }

        $managersalle = new SalleDAO($this->getPdo());
        $salle = $managersalle->find($id);

        if ($salle->getRangCourant() == null){
            $salle->setRangCourant(1);

            // modifier le fichier json
            $jsonData = file_get_contents("jsonW2G/salle$id.json");
            $data = json_decode($jsonData, true);
            preg_match('/(?:v=|\/)([a-zA-Z0-9_-]{11})/', $url, $matches);
            if (isset($matches[1])) {
                // $matches[1] contient l'ID de la vidéo
                $idVideo= $matches[1];
            } else {
                preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches);
                // $matches[1] contient l'ID de la vidéo
                $idVideo= $matches[1];
            }
            $data['video']['id'] = $idVideo;
            file_put_contents("jsonW2G/salle$id.json", json_encode($data));

        }
        
        $managersalle->update($salle);

        $managersalle->ajouterVideo($salle->getIdSalle(),$url);

    }
    

}