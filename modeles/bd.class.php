<?php

/**
 * Classe Singleton Bd
 *
 * Cette classe gère la connexion à une base de données MySQL en utilisant PDO.
 * Elle implémente le design pattern Singleton pour s'assurer qu'il n'existe qu'une
 * seule instance de la connexion à la base de données.
 */
class Bd
{
    /**
     * Instance unique de la classe Bd.
     * @var Bd|null
     */
    private static ?Bd $instance = null;

    /**
     * Instance PDO représentant la connexion à la base de données.
     * @var PDO|null
     */
    private ?PDO $pdo;
    /**
     * Date de la dernière sauvegarde de la base de données.
     * @var DateTime|null
     */
    private ?DateTime $lastBackup = null;


    /**
     * Constructeur privé pour empêcher l'instanciation directe de la classe.
     * Initialise la connexion à la base de données avec PDO.
     * 
     * @throws PDOException Si la connexion à la base de données échoue.
     */
    private function __construct(){
        try {

            $this->pdo = new PDO(
                'mysql:host='.DB_HOST.';dbname='. DB_NAME,
                DB_USER,
                DB_PASS,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4')
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sauvegarde de la base de données
            $this->lastBackup = $this->sauvegarder();
        } catch (PDOException $e) {
            die('Connexion à la base de données échouée : ' . $e->getMessage());
        }
        
    }

    /**
     * Récupère l'instance unique de la classe Bd.
     *
     * @return Bd L'instance unique de la classe.
     */
    public static function getInstance(): Bd{
        if (self::$instance == null){
            self::$instance = new Bd();
        }
        return self::$instance;
    }

    /**
     * Récupère l'objet PDO représentant la connexion à la base de données.
     *
     * @return PDO L'instance PDO pour effectuer des opérations sur la base de données.
     */
    public function getConnexion(): PDO
    {
        return $this->pdo;
    }

    /**
     * Empêche le clonage de l'instance Bd.
     *
     * Cette méthode est privée pour empêcher la création d'une nouvelle
     * instance via le clonage de l'objet.
     */
    private function __clone()
    {

    }

    /**
     * Empêche la désérialisation de l'instance Bd.
     *
     * @throws Exception Si une tentative de désérialisation est effectuée.
     */
    public function __wakeup()
    {
        throw new Exception("Un singleton ne doit pas être deserialisé");
    }

    public function sauvegarder(): ?string
{
    $date = new DateTime();

    // Vérifier si une sauvegarde a déjà été faite récemment
    if ($this->lastBackup !== null && $this->lastBackup->diff($date)->days < 7) {
        echo "Pas besoin de sauvegarde. Dernière sauvegarde : " . $this->lastBackup->format('Y-m-d H:i:s') . "\n";
        return null;
    }

    // Nom du fichier de sauvegarde
    $filename = 'backup_' . $date->format('Y-m-d_H-i-s') . '.sql';
    $path = '/backupsBD/' . $filename; 
    
    try {
        $pdo = $this->getConnexion();
        $sql = "-- Sauvegarde de la base " . DB_NAME . " - " . $date->format('Y-m-d H:i:s') . "\n\n";

        // Désactiver les contraintes de clés étrangères
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

        // 1️⃣ **Lister les tables**
        $tables = [];
        $result = $pdo->query("SHOW TABLES");
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $tableStructures = [];
        $tableData = [];

        foreach ($tables as $table) {
            // 2️⃣ **Exporter la structure de la table**
            $res = $pdo->query("SHOW CREATE TABLE `$table`");
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $tableStructures[$table] = "-- Structure de la table `$table` --\n" . $row['Create Table'] . ";\n\n";

            // 3️⃣ **Exporter les données**
            $res = $pdo->query("SELECT * FROM `$table`");
            while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                $values = array_map([$pdo, 'quote'], array_values($row));
                $tableData[$table][] = "INSERT INTO `$table` (`" . implode("`, `", array_keys($row)) . "`) VALUES (" . implode(", ", $values) . ");";
            }
        }

        // Écrire d'abord toutes les structures
        foreach ($tableStructures as $table => $createSQL) {
            $sql .= $createSQL;
        }

        // Activer les contraintes de clés étrangères
        $sql .= "\nSET FOREIGN_KEY_CHECKS = 1;\n\n";

        // Ajouter les données après les créations de tables
        foreach ($tableData as $table => $data) {
            $sql .= "-- Données de la table `$table` --\n";
            $sql .= implode("\n", $data) . "\n\n";
        }

        // 4️⃣ **Sauvegarder dans un fichier**
        if (!is_dir( '../backupsBD')) {
            mkdir('../backupsBD', 0777, true);
        }

        file_put_contents($path, $sql);

        // Mise à jour de la dernière sauvegarde
        $this->lastBackup = $date;
        echo "Sauvegarde réussie : $path\n";

        return $path;
    } catch (Exception $e) {
        echo "Erreur lors de la sauvegarde : " . $e->getMessage() . "\n";
    }
}
}
