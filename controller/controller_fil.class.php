<?php

// Contrôleur de Fil 
class ControllerFil extends Controller {
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    public function listerThreads(){
        $filDAO = new FilDAO($this->getPdo());
        $threads = $filDAO->findAll();

        echo $this->getTwig()->render('forum.html.twig', [
            'fils' => $threads,
            'test' => 'test'
        ]);
    }

    // Méthode d'affichage d'un fil par son id
    public function afficherFilParId(){
        $id = $_GET['id'];
        $filDAO = new FilDAO($this->getPdo());
        $fil = $filDAO->findMessagesByFilId($id);

        echo $this->getTwig()->render('fil.html.twig', [
            'fil' => $fil
        ]);
    }
}
