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
    private function __clone() {}

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
                $tableStructures[$table] = "-- Structure de la table `$table` --\n DROP TABLE IF EXISTS `$table`; \n" . $row['Create Table'] . ";\n\n";

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
        $oldestBackupDate = null;
        $latestBackupDate = null;
        $nombreBackups = 0;

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
                    if ($oldestBackupDate === null || $date < $oldestBackupDate) {
                        $oldestBackup = $file;
                        $oldestBackupDate = $date;
                    }

                    $nombreBackups++;
                }
            }
        }

        var_dump("Dernière backup trouvée : ", $latestBackupDate);
        $now = new DateTime();
        var_dump("Date actuelle : ", $now);

        var_dump("=== ÉVALUATION CONDITION ===");
        if ($latestBackupDate === null) {
            var_dump("Pas de backup existante - sauvegarde nécessaire");
            $this->sauvegarder();
        } else {
            $interval = $latestBackupDate->diff($now);
            var_dump("Différence en jours : " . $interval->days);
            var_dump("Condition $interval->days >= 2 : " . ($interval->days >= 2 ? 'true' : 'false'));

            if ($interval->days >= 2) {
                var_dump("=== SAUVEGARDE DÉCLENCHÉE ===");
                if ($nombreBackups > SAUVEGARDE_MAX_NUMBER) {
                    var_dump("Suppression ancienne backup");
                    unlink($backupDir . '/' . $oldestBackup);
                }
                $this->sauvegarder();
                var_dump("=== SAUVEGARDE EFFECTUÉE ===");
            } else {
                var_dump("=== PAS DE SAUVEGARDE NÉCESSAIRE ===");
            }
        }
        var_dump("=== FIN VÉRIFICATION ===");
    }



    /**
     * @brief méthode de restauration de la base de données
     * 
     * @param string $fileToRestore Nom du fichier à restaurer
     * 
     * @throws Exception e En cas d'echec de la restoration
     * 
     * @return void
     */
    public function restore(string $fileToRestore)
    {
        $backupFolder = "backupsBD/";
        if (!file_exists($backupFolder . $fileToRestore)) {
            throw new Exception("Fichier de backup non trouvé");
        }

        // Désactiver les contraintes de clés étrangères
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

        // Lire et exécuter le fichier SQL
        $sqlContent = file_get_contents($fileToRestore);
        $sqlStatements = explode(';', $sqlContent);

        foreach ($sqlStatements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $this->pdo->exec($statement);
                } catch (PDOException $e) {
                    // Log ou gérer l'erreur selon vos besoins
                    error_log("Erreur lors de la restauration : " . $e->getMessage());
                }
            }
        }

        // Réactiver les contraintes de clés étrangères
        $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    }
}
