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
    private function __construct()
    {
        try {

            $this->pdo = new PDO(
                'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS,
                array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4')
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Sauvegarde de la base de données
            $this->verifierSauvegarde();
        } catch (PDOException $e) {
            die('Connexion à la base de données échouée : ' . $e->getMessage());
        }

    }

    /**
     * Récupère l'instance unique de la classe Bd.
     *
     * @return Bd L'instance unique de la classe.
     */
    public static function getInstance(): Bd
    {
        if (self::$instance == null) {
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

    /**
     * Sauvegarde la base de données dans un fichier SQL.
     * 
     * Cette méthode crée un fichier de sauvegarde de la base de données dans le dossier `backupsBD`.
     * Le fichier contient la structure des tables et les données de chaque table.
     * @return void
     */
    public function sauvegarder(): void
    {
        $date = new DateTime();

        // Nom du fichier de sauvegarde
        $filename = 'backup_' . $date->format('Y-m-d_H-i-s') . '.sql';
        $path = "../backupsBD/{$filename}";

        try {
            $pdo = $this->getConnexion();
            $sql = "-- Sauvegarde de la base " . DB_NAME . " - " . $date->format('Y-m-d H:i:s') . "\n\n";

            // Désactiver les contraintes de clés étrangères
            $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";

            // Lister les tables
            $tables = [];
            $result = $pdo->query("SHOW TABLES");
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tables[] = $row[0];
            }

            $tableStructures = [];
            $tableData = [];

            foreach ($tables as $table) {
                // Exporter la structure de la table
                $res = $pdo->query("SHOW CREATE TABLE `$table`");
                $row = $res->fetch(PDO::FETCH_ASSOC);
                $tableStructures[$table] = "-- Structure de la table `$table` --\n" . $row['Create Table'] . ";\n\n";

                // Exporter les données
                $res = $pdo->query("SELECT * FROM `$table`");
                while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                    $values = array_map(function ($value) use ($pdo) {
                        return $value === null ? 'NULL' : $pdo->quote($value);
                    }, array_values($row));
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

            // Sauvegarder dans un fichier
            $backupDir = realpath(dirname(__FILE__)) . '/../backupsBD';
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0777, true);
            }

            file_put_contents($backupDir . '/' . $filename, $sql);

            // Mise à jour de la dernière sauvegarde
            $this->lastBackup = $date;
        } catch (Exception $e) {
            echo "Erreur lors de la sauvegarde : " . $e->getMessage() . "\n";
        }
    }


    /**
     * Méthode pour récupérer la date de la dernière sauvegarde de la base de données depuis les fichiers de sauvegarde.
     * 
     * Cette méthode parcourt les fichiers de sauvegarde dans le dossier `backupsBD` pour trouver le fichier le plus récent, puis vérifie si la base doit être sauvegardée. Lance la sauvegarde si nécessaire.
     * 
     * @return void
     */
    private function verifierSauvegarde(): void
    {
        // Récupérer la date de la dernière sauvegarde
        $backupDir = realpath(dirname(__FILE__)) . '/../backupsBD';
        $latestBackup = null;
        $oldestBackup = null;
        $latestBackupDate = null;
        $nombreBackups = 0;
        $deleteOldest = false;

        if (is_dir($backupDir)) {
            $files = scandir($backupDir);
            foreach ($files as $file) {
                if (preg_match('/^backup_(\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2})\.sql$/', $file, $matches)) {
                    $date = DateTime::createFromFormat('Y-m-d_H-i-s', $matches[1]);

                    // Récupérer la dernière backup
                    if ($latestBackupDate === null || $date > $latestBackupDate) {
                        $latestBackup = $file;
                        $latestBackupDate = $date;
                    }

                    // Récupérer la plus ancienne backup si on doit être amené à la supprimer
                    if ($oldestBackup === null || $date < $oldestBackup) {
                        $oldestBackup = $file;
                    }

                    $nombreBackups++;
                }
            }
        }

        // Vérifier si la base doit être sauvegardée
        if ($latestBackupDate === null || $latestBackupDate->diff(new DateTime())->days >= 2) {
            // Supprimer l'ancienne sauvegarde si on en a trop
            if ($nombreBackups > SAUVEGARDE_MAX_NUMBER) {
                unlink($backupDir . '/' . $oldestBackup);
            }

            $this->sauvegarder();
        }


    }

}
