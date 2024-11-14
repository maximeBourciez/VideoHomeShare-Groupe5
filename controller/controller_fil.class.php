<?php

/**
 * @brief Classe ControllerFil
 * 
 * @details Classe permettant de gérer les actions liées aux fils de discussion du forum
 * 
 * @date 13/11/2020
 * 
 * @version 1.0
 * 
 * @note Classe héritant de la classe Controller
 * 
 * @author Maxime Bourciez <maxime.bourciez@gmail.com>
 */
class ControllerFil extends Controller {

    /**
     * @brief Constructeur de la classe ControllerFil
     * 
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader){
        parent::__construct($twig, $loader);
    }

    /**
     * @brief Méthode de listing des fils de discussion
     * 
     * @return void
     */
    public function listerThreads(){
        $filDAO = new FilDAO($this->getPdo());
        $threads = $filDAO->findAll();

        echo $this->getTwig()->render('forum.html.twig', [
            'fils' => $threads,
            'test' => 'test'
        ]);
    }

    /**
     * @brief Méthode d'affichage d'un fil de discussion par son identifiant
     * 
     * @details Méthode permettant d'afficher un fil de discussion par son identifiant, et ainsi de permettre l'afficahge de la discussion sous-jacente
     *
     * @return void
     */
    public function afficherFilParId(){
        $id = $_GET['id'];

        // Récuérer les messages du fil
        $messageDAO = new MessageDAO($this->getPdo());
        $messages = $messageDAO->listerMessagesParFil($id);

        // Récupérer les infos du fil
        $filDAO = new FilDAO($this->getPdo());
        $fil = $filDAO->findById($id);

        foreach ($messages as $message) {
            // Récupérer l'utilisateur associé au message
            $createur = $message->getUser();
            var_dump($createur);  // Vérifiez que l'utilisateur est bien là
        }

        echo $this->getTwig()->render('fil.html.twig', [
            'fil' => $fil,
            'messages' => $messages
        ]);
    }
}
