<?php
session_start();
// Inclure tous les modèles & contrôleurs
require_once "include.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (
    !isset($_SESSION['utilisateur']) &&
    !str_contains($_SERVER['REQUEST_URI'], 'controller=utilisateur&methode=connexion') &&
    !str_contains($_SERVER['REQUEST_URI'], 'controller=utilisateur&methode=checkInfoConnecter')
) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
}


// Créer le contrôleur demandé
try  {
    if(isset($_GET['controller'])){
        $controllerName=$_GET['controller'];
    }else{
        $controllerName='';
    }

    if (isset($_GET['methode'])){
        $methode=$_GET['methode'];
    }else{
        $methode='';
    }

    //Gestion de la page d'accueil par défaut
    if ($controllerName == '' && $methode ==''){
        $controllerName='index';
        $methode='index';
    }

    if ($controllerName == '' ){
        throw new Exception('Le controleur n\'est pas défini');
    }

    if ($methode == '' ){
        throw new Exception('La méthode n\'est pas définie');
    }



    
    if ( isset($_SESSION['utilisateur'])) {
        $twig->addGlobal('utilisateurConnecte', unserialize($_SESSION['utilisateur']));
    }else {
        $twig->addGlobal('utilisateurConnecte', null);
    }

    $controller = ControllerFactory::getController($controllerName, $loader, $twig);
    $controller->call($methode);

}catch (Exception $e) {
   die('Erreur : ' . $e->getMessage());
}
