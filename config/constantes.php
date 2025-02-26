<?php

use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';

// Chargement du fichier YAML
try {
    $config = Yaml::parseFile('config/constantes.yaml');
} catch (Exception $e) {
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
define('SECRET_KEY', $config['security']['secret_key']);
define('WEBSITE_MAIL', $config['website']['website_mail']);


// Constantes de l'API TMDB
define('TMDB_API_KEY', $config['tmdb']['api_key']);
define('TMDB_BASE_URL', $config['tmdb']['base_url']);
define('TMDB_IMAGE_BASE_URL', $config['tmdb']['image_base_url']);

// Constante pour les threads
define('VALEUR_MESSAGE_SUPPRIME', $config['threads']['valeurMessageSupprime']);
define('VALEUR_UTILISATEUR_MESSAGE_SUPPRIME', $config['threads']['valeurUtilisateurMessageSupprime']);
define('NOMBRE_MESSAGES_PAR_PAGE', $config['threads']['nombreMessagesParPage']);

// Constante pour la sauvegarde
define('SAUVEGARDE_MAX_NUMBER', $config['backup']['nombreSauvegardesMax']);
define('SAUVEGARDE_CHEMIN', $config['backup']['chemin']);
