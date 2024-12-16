<?php


class ControllerWhatchlist extends Controller
{
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        parent::__construct($twig, $loader);
    }

    /**
     * @bref permet d'afficher la page d'accueil des watchlists
     *
     * @return void
     */
    public function accueil(): void
    {

        //Génération de la vue
        $template = $this->getTwig()->load('whatchlists.html.twig');
        echo $template->render(array());
    }

}