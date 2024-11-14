<?php
/**
 * @brief Classe ControllerFactory
 * 
 * @details Classe permettant de générer un controller en fonction de son nom
 * 
 * @date 5/11/2024
 * 
 * @warning Cette classe met en place une version simplifiée du design pattern Factory
 */
class ControllerFactory
{
    /**
     * @brief Méthode permettant de générer un controller en fonction de son nom
     *
     * @param [type] $controleur
     * @param \Twig\Loader\FilesystemLoader $loader
     * @param \Twig\Environment $twig
     * @return Controller|null
     */
    public static function getController($controleur, \Twig\Loader\FilesystemLoader $loader, \Twig\Environment $twig)
    {
        $controllerName="Controller".ucfirst($controleur);
        if (!class_exists($controllerName)) {
            throw new Exception("Le controleur $controllerName n'existe pas");
        }
        return new $controllerName($twig, $loader);

    }
}