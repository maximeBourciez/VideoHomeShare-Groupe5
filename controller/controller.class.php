<?php
/**
 * @brief Classe Controller
 * @details Classe mère permettant de gérer tous les controllers de l'application
 * 
 * @date 13/11/2024
 * 
 * @version 1.2
 */
class Controller
{
    /**
     * @var PDO|null $pdo Connexion à la base de données
     */
    private PDO $pdo;
    /**
     * @var \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    private \Twig\Loader\FilesystemLoader $loader;
    /**
     * @var \Twig\Environment $twig Environnement Twig
     */
    private \Twig\Environment $twig;
    /**
     * @var array|null $get Tableau $_GET
     */
    private ?array $get = null;
    /**
     * @var array|null $post Tableau $_POST
     */
    private ?array $post = null;


    /**
     * Constructeur
     * @details Initialise les attributs de la classe + créé une connexion à la base de données
     * @param \Twig\Environment $twig Environnement Twig
     * @param \Twig\Loader\FilesystemLoader $loader Chargeur de templates
     */
    public function __construct(\Twig\Environment $twig, \Twig\Loader\FilesystemLoader $loader)
    {
        $db = Bd::getInstance();
        $this->pdo = $db->getConnexion();

        $this->loader = $loader;
        $this->twig = $twig;

        if (isset($_GET) && !empty($_GET)) {
            $this->get = $_GET;
        }
        if (isset($_POST) && !empty($_POST)) {
            $this->post = $_POST;
        }
    }

    /**
     * 
     * @details Appelle une méthode du controller courant
     * @param string $methode Nom de la méthode à appeler
     * @return mixed Résultat de la méthode appelée
     */
    public function call(string $methode): mixed
    {

        try {
            if (!method_exists($this, $methode)) {
                throw new Exception("methodeInexistante");
            }
            return $this->$methode();
            
        } catch (Exception $e) {
            // Traiter l'exception levée
            switch ($e->getMessage()) {
                case "methodeInexistante":
                    $this->twig->render("404.html.twig");
                    break;
                case "accesInterdit":
                    $this->twig->render("403.html.twig");
                    break;
                case "enConstruction":
                    $this->twig->render("construction.html.twig");
                    break;
                default:
                    $this->twig->render("500.html.twig");
                    break;
            }
            return null;
        }



    }




    /**
     * Get the value of pdo
     * @return PDO|null
     */
    public function getPdo(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * Set the value of pdo
     * @param PDO|null $pdo
     */
    public function setPdo(?PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    /**
     * Get the value of loader
     * @return \Twig\Loader\FilesystemLoader
     */
    public function getLoader(): \Twig\Loader\FilesystemLoader
    {
        return $this->loader;
    }

    /**
     * Set the value of loader
     * @param \Twig\Loader\FilesystemLoader $loader
     */
    public function setLoader(\Twig\Loader\FilesystemLoader $loader): void
    {
        $this->loader = $loader;
    }



    /**
     * Get the value of twig
     * @return \Twig\Environment
     */
    public function getTwig(): \Twig\Environment
    {
        return $this->twig;
    }

    /**
     * Set the value of twig
     * @param \Twig\Environment $twig
     */
    public function setTwig(\Twig\Environment $twig): void
    {
        $this->twig = $twig;

    }



    /**
     * Get the value of get
     * @return array|null
     */
    public function getGet(): ?array
    {
        return $this->get;
    }

    /**
     * Set the value of get
     * @param array|null $get
     */
    public function setGet(?array $get): void
    {
        $this->get = $get;

    }

    /**
     * Get the value of post
     * @return array|null $post
     */
    public function getPost(): ?array
    {
        return $this->post;
    }

    /**
     * Set the value of post
     * @param array|null $post
     */
    public function setPost(?array $post): void
    {
        $this->post = $post;


    }
}