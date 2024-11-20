<?php

use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';

// Chargement du fichier YAML
try{
    $config = Yaml::parseFile('config/template_constantes.yaml');
}
catch(Exception $e)
{
    echo "Pas de fichier Yaml.";
}
// Utilisation des constantes

//Connexion à la base de données
define('DB_HOST', $config['database']['host']);
define('DB_NAME', $config['database']['name']);
define('DB_USER', $config['database']['user']);
define('DB_PASS', $config['database']['password']);
define('DB_PREFIX', $config['database']['prefix']);


// Constante de la partie vue
define('WEBSITE_TITLE', $config['website']['title']);
define('WEBSITE_TITLE_LONG', $config['website']['title_long']);
define('WEBSITE_LANGUAGE', $config['website']['language']);
define('WEBSITE_LOGO', $config['website']['logo']);
define('WEBSITE_LOGO_FLAG', $config['website']['logo_flag']);
define('WEBSITE_LINK', $config['website']['website_link']);
?>
