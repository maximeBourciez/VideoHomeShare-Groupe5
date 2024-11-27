<?php
require 'vendor/autoload.php';
require 'constantes.php';

use Symfony\Component\Yaml\Yaml;

try {
    // Connexion à la base de données avec PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='. DB_NAME, DB_USER, DB_PASS, $options);

    // Requête SQL pour récupérer les préférences utilisateurs
    $query = "SELECT idUtilisateur, pseudo, FROM ".DB_NAME."_utilisateur";
    $stmt = $pdo->query($query);

    // Transformation des données en tableau associatif
    $preferences = [];
    while ($row = $stmt->fetch()) {
        $preferences[$row['idUtilisateur']] = [
            'pseudo' => $row['theme'],
            'notifications' => [
                'email' => false,
            ],
        ];
    }

    // Génération du fichier YAML
    $yamlContent = Yaml::dump($preferences, 4, 2);
    $outputFile = 'preferences.yaml';
    file_put_contents($outputFile, $yamlContent);

    echo "Fichier YAML généré avec succès : $outputFile\n";

} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Erreur lors de la génération du fichier YAML : " . $e->getMessage() . "\n";
}
