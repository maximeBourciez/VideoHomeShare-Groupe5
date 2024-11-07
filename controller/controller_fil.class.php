<?php

// Contrôleur de Fil 
class ControllerFil extends Controller{
    // Constructeur
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    // Méthodes 
    public function listerThreads(){
        // Récupérer tous les fils de la DB
        $filDAO = new FilDAO($this->getPdo());
        $threads = $filDAO->findAll();

        // Afficher la vue
        echo $this->getTwig()->render('forum.html.twig', [
            'fils' => $threads
        ]);
    }
}