<?php
/**
 * @file utilisateur.dao.php
 * 
 * @brief Utilisateur Data Access Object
 * 
 * @details Data Access Object de la classe Utilisateur
 * 
 * @version 1.0
 * 
 * @date 18/12/2024
 * 
 * @author Sylvain Trouilh <strouilh@iutbayonne.univ-pau.fr>
 */

class UtilisateurDAO
{
    /** @var PDO|null Instance de connexion à la base de données */
    private $pdo;

    /**
     * @brief Constructeur de la classe UtilisateurDAO
     * 
     * @param PDO|null $pdo Instance PDO pour l'accès à la base de données
     */
    function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }
    /**
     * @brief crée un utilisateur dans la base de données
     * @param Utilisateur $utilisateur L'utilisateur que l'on souhaite créer
     * @return bool true si l'utilisateur a été créé, false sinon
     */
    function create(Utilisateur $utilisateur): bool
    {
        // Préparation de la requête
        $pdo = $this->pdo->prepare("INSERT INTO " . DB_PREFIX . "utilisateur 
        (idUtilisateur, pseudo, vraiNom, mail, mdp, role, urlImageProfil, urlImageBanniere , dateI  ,estValider) 
        VALUES (:idUtilisateur, :pseudo , :mail, :mdp, :role, :urlImageProfil, :urlImageBanniere , NOW() , 0)");
      
        // Récupération des valeurs
        $id = $utilisateur->getId();
        $pseudo = $utilisateur->getPseudo();
        $mail = $utilisateur->getMail();
        $mdp = $utilisateur->getMdp();
        $role = $utilisateur->getRole()->toString();
        $urlImageProfil = $utilisateur->getUrlImageProfil();
        $urlImageBanniere = $utilisateur->getUrlImageBanniere();

        // passage des paramètres
        $pdo->bindValue(":idUtilisateur", $id);
        $pdo->bindValue(":pseudo", $pseudo);
        $pdo->bindValue(":mail", $mail);
        $pdo->bindValue(":mdp", $mdp);
        
        $pdo->bindValue(":role", $role);
        $pdo->bindValue(":urlImageProfil", $urlImageProfil);
        $pdo->bindValue(":urlImageBanniere", $urlImageBanniere);

        // execution de la requête
        return $pdo->execute();
    }

    /**
     * @brief Met à jour un utilisateur dans la base de données
     * @param Utilisateur $utilisateur L'utilisateur que l'on souhaite mettre à jour
     * @return bool true si l'utilisateur a été mis à jour, false sinon
     */
    function update(Utilisateur $utilisateur): bool
    {

        $pdo = $this->pdo->prepare("UPDATE " . DB_PREFIX . "utilisateur SET  pseudo = :pseudo, mail = :mail, mdp = :mdp,  urlImageProfil = :urlImageProfil, urlImageBanniere = :urlImageBaniere, estValider = :estValider WHERE idUtilisateur = :id");

        $id = $utilisateur->getId();
        $pseudo = $utilisateur->getPseudo();
        $mail = $utilisateur->getMail();
        $mdp = $utilisateur->getMdp();
        $urlImageProfil = $utilisateur->getUrlImageProfil();
        $urlImageBaniere = $utilisateur->getUrlImageBanniere();
        $estValider = $utilisateur->getEstValider();
        $pdo->bindParam(":id", $id);
        $pdo->bindParam(":pseudo", $pseudo);
        $pdo->bindParam(":mail", $mail);
        $pdo->bindParam(":mdp", $mdp);
        $pdo->bindParam(":urlImageProfil", $urlImageProfil);
        $pdo->bindParam(":urlImageBaniere", $urlImageBaniere);
        $pdo->bindParam(":estValider", $estValider);

        return $pdo->execute();
    }

    /**
     * @brief Supprime un utilisateur de la base de données
     * @param int $id L'identifiant de l'utilisateur à supprimer
     * @return bool true si l'utilisateur a été supprimé, false sinon
     */
    function delete(int $id): bool
    {
        $req = $this->pdo->prepare("DELETE FROM " . DB_PREFIX . "utilisateur WHERE id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }

    /**
     * @brief Hydrate un utilisateur à partir d'un tableau associatif
     * @param array $row Tableau associatif contenant les données de l'utilisateur
     * @return Utilisateur L'utilisateur hydraté
     */
    function hydrate(array $row): Utilisateur
    {
        // Récupération des valeurs
        $id = $row['idUtilisateur'];
        $pseudo = $row['pseudo'];
        $mail = $row['mail'];
        $mdp = $row['mdp'];
        $role = $row['role'];
       


        // Transformer le role
        $roleEnum = Role::fromString($role);
        if ($roleEnum !== null) {
            $role = $roleEnum;
        }

        $urlImageProfil = $row['urlImageProfil'];
        $urlImageBanniere = $row['urlImageBanniere'];
        $estValider = $row['estValider'];
        // Retourner l'utilisateur
        return new Utilisateur($id, $pseudo,  $mail, $mdp, $role, $urlImageProfil, $urlImageBanniere, $estValider);

    }
    
    /**
     * @brief Hydrate tous les utilisateurs à partir d'un tableau de tableaux associatifs
     * @param array $rows Tableau de tableaux associatifs contenant les données des utilisateurs
     * @return Utilisateur[] Tableau d'utilisateurs hydratés
     */
    function hydrateAll(array $rows): array
    {
        $utilisateurs = [];
        foreach ($rows as $row) {
            $utilisateur = $this->hydrate($row);
            array_push($utilisateurs, $utilisateur);  // Ajout de l'utilisateur au tableau 
        }
        return $utilisateurs;
    }

    /**
     * @brief Recherche un utilisateur dans la base de données à partir de son identifiant
     * @param string|null $id L'identifiant de l'utilisateur à rechercher
     * @return Utilisateur|null L'utilisateur trouvé, null sinon
     */
    function find(?string $id): ?Utilisateur
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "utilisateur WHERE idUtilisateur = :id";
        $pdo = $this->pdo->prepare($sql);
        $pdo->bindValue(':id', $id, PDO::PARAM_STR);
        $pdo->execute();
        $pdo->setFetchMode(PDO::FETCH_ASSOC);
        $row = $pdo->fetch();
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * @brief Recherche tous les utilisateurs dans la base de données
     * @return Utilisateur[] Tableau d'utilisateurs trouvés
     */
    function findAll(): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "utilisateur";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }


    /**
     * @brief Recherche un utilisateur dans la base de données à partir de son mail et de son mot de passe
     * @param string $mail L'adresse mail de l'utilisateur à rechercher
     * @param string $MDP Le mot de passe de l'utilisateur à rechercher
     * @return int Le nombre d'utilisateurs trouvés
     */
    public function findByMailandPWD(String  $mail, String $MDP): int
    {
        $sql = "SELECT count(idUtilisateur) FROM " . DB_PREFIX . "utilisateur WHERE mail = :mail AND mdp = :mdp";
        $pdo = $this->pdo->prepare($sql);
        $pdo->bindValue(':mail', $mail, PDO::PARAM_STR);
        $pdo->bindValue(':mdp', $MDP, PDO::PARAM_STR);
        $pdo->execute();
        return $pdo->fetch()["count(idUtilisateur)"];
    }

    /**
     * @brief Recherche un utilisateur dans la base de données à partir de son mail
     * @param string $mail L'adresse mail de l'utilisateur à rechercher
     * @return Utilisateur|null L'utilisateur trouvé, null sinon
     */
    public function findByMail(String  $mail): ?Utilisateur
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "utilisateur WHERE mail = :mail";
        $pdo = $this->pdo->prepare($sql);
        $pdo->bindValue(':mail', $mail, PDO::PARAM_STR);
        $pdo->execute();
        $pdo->setFetchMode(PDO::FETCH_ASSOC);
        $row = $pdo->fetch();
        if ($row) {
            return $this->hydrate($row);
        }
        return null;
    }

    /**
     * @brief suprime les utilisateurs non confirmés au bout de 24h
     * @param string $pseudo Le pseudo de l'utilisateur à rechercher
     * @return Utilisateur|null L'utilisateur trouvé, null sinon
     */
    public function deleteUtilisateurnonconfirme(): bool
    {
        $sql = "DELETE FROM " . DB_PREFIX . "utilisateur WHERE (NOW()-dateI)/3600 >= 24 and estValider = 0";
        $pdo = $this->pdo->prepare($sql);
        return $pdo->execute();
    }

    /**
     * @brief Vérifie si un utilisateur est validé
     * @param string $id L'identifiant de l'utilisateur à vérifier
     * @return bool true si l'utilisateur est validé, false sinon
     */
    public function verificationUtilisateurValide(String $id): bool
    { 
        $sql = "select estValider from " . DB_PREFIX . "utilisateur WHERE idUtilisateur = :id";
        $pdo = $this->pdo->prepare($sql);
        $pdo->bindValue(':id', $id, PDO::PARAM_STR);
        $pdo->execute();
        $row = $pdo->fetch();
        return $row["estValider"];
    }
}
