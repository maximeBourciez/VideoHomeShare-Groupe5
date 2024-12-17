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
}
