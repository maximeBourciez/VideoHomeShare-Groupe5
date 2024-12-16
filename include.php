<?php

// Ajout de l'autoload de composer
require_once 'vendor/autoload.php';

// Ajout du fichier constantes qui permet de configurer le site
require_once 'config/constantes.php';

// Ajout du code pour initialiser twig
require_once 'config/twig.php';

// Ajout de la BD
require_once 'modeles/bd.class.php';

// Ajout des modèles DAO
require_once 'modeles/fil.dao.php';
require_once 'modeles/message.dao.php';
require_once 'modeles/utilisateur.dao.php';
require_once 'modeles/contenu.dao.php';
require_once 'modeles/watchlist.dao.php';
require_once 'modeles/personnalite.dao.php';
require_once 'modeles/quizz.dao.php';
require_once 'modeles/question.dao.php';
require_once 'modeles/theme.dao.php';
require_once 'modeles/commentaire.dao.php';
require_once 'modeles/personnalite.dao.php';

// Ajout des modèles
require_once 'modeles/fil.class.php';
require_once 'modeles/message.class.php';
require_once 'modeles/utilisateur.class.php';
require_once 'modeles/contenu.class.php';
require_once 'modeles/watchlist.class.php';
require_once 'modeles/personnalite.class.php';
require_once 'modeles/quizz.class.php';
require_once 'modeles/question.class.php';
require_once 'modeles/theme.class.php';
require_once 'modeles/commentaire.class.php';
require_once 'modeles/personnalite.class.php';


// Ajout des contrôleurs
require_once 'controller/controller.class.php';
require_once 'controller/controller_factory.class.php';
require_once 'controller/controller_fil.class.php';
require_once 'controller/controller_utilisateur.class.php';
require_once 'controller/controller_contenu.class.php';
require_once 'controller/controller_watchlist.class.php';

