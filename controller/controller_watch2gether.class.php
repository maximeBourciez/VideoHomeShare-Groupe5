<?php


class ControllerWatch2Gether extends Controller
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
        //$managersalle = new SalleDAO($this->getPdo());
        $managertheme = new ThemeDAO($this->getPdo());

        $themes = $managertheme->findAll();
        $template = $this->getTwig()->load('acceilWatch2Gether.html.twig');
        echo $template->render(array('themes' => $themes));
    }
}