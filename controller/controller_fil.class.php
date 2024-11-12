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
}
