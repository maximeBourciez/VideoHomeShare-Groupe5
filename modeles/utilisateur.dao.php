<?php

class UtilisateurDAO
{
    private $pdo;

    function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
    }

    function create(Utilisateur $utilisateur): bool
    {
        // Préparation de la requête
        $pdo = $this->pdo->prepare("INSERT INTO " . DB_PREFIX . "utilisateur 
        (idUtilisateur, pseudo, vraiNom, mail, mdp, role, urlImageProfil, urlImageBanniere) 
        VALUES (:idUtilisateur, :pseudo, :nom , :mail, :mdp, :role, :urlImageProfil, :urlImageBaniere)");


        // Récupération des valeurs
        $id = $utilisateur->getId();
        $pseudo = $utilisateur->getPseudo();
        $nom = $utilisateur->getNom();
        $mail = $utilisateur->getMail();
        $mdp = $utilisateur->getMdp();
        $role = $utilisateur->getRole();
        $urlImageProfil = $utilisateur->getUrlImageProfil();
        $urlImageBaniere = $utilisateur->getUrlImageBaniere();


        // passage des paramètres
        $pdo->bindValue(":idUtilisateur", $id);
        $pdo->bindValue(":pseudo", $pseudo);
        $pdo->bindValue(":nom", $nom);
        $pdo->bindValue(":mail", $mail);
        $pdo->bindValue(":mdp", $mdp);
        $pdo->bindValue(":role", $role);
        $pdo->bindValue(":urlImageProfil", $urlImageProfil);
        $pdo->bindValue(":urlImageBaniere", $urlImageBaniere);

        // execution de la requête
        return $pdo->execute();
    }

    function update(Utilisateur $utilisateur): bool
    {
        $pdo = $this->pdo->prepare("UPDATE " . DB_PREFIX . "utilisateur SET  pseudo = :pseudo, vraiNom = :nom, mail = :mail, mdp = :mdp,  urlImageProfil = :urlImageProfil, urlImageBanniere = :urlImageBaniere WHERE idUtilisateur = :id");

        $id = $utilisateur->getId();
        $pseudo = $utilisateur->getPseudo();
        $nom = $utilisateur->getNom();
        $mail = $utilisateur->getMail();
        $mdp = $utilisateur->getMdp();
        $urlImageProfil = $utilisateur->getUrlImageProfil();
        $urlImageBaniere = $utilisateur->getUrlImageBaniere();
        $pdo->bindParam(":id", $id);
        $pdo->bindParam(":pseudo", $pseudo);
        $pdo->bindParam(":nom", $nom);
        $pdo->bindParam(":mail", $mail);
        $pdo->bindParam(":mdp", $mdp);
        $pdo->bindParam(":urlImageProfil", $urlImageProfil);
        $pdo->bindParam(":urlImageBaniere", $urlImageBaniere);
        return $pdo->execute();
    }

    function delete(int $id): bool
    {
        $req = $this->pdo->prepare("DELETE FROM " . DB_PREFIX . "utilisateur WHERE id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }

    function hydrate(array $row): Utilisateur
    {
        // Récupération des valeurs
        $id = $row['idUtilisateur'];
        $pseudo = $row['pseudo'];
        $nom = $row['vraiNom'];
        $mail = $row['mail'];
        $mdp = $row['mdp'];
        $role = $row['role'];
        $urlImageProfil = $row['urlImageProfil'];
        $urlImageBaniere = $row['urlImageBanniere'];

        // Retourner l'utilisateur
        return new Utilisateur($id, $pseudo, $nom, $mail, $mdp, $role, $urlImageProfil, $urlImageBaniere);
    }

    function hydrateAll(array $rows): array
    {
        $utilisateurs = [];
        foreach ($rows as $row) {
            $utilisateur = $this->hydrate($row);
            array_push($utilisateurs, $utilisateur);  // Ajout de l'utilisateur au tableau 
        }
        return $utilisateurs;
    }

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

    function findAll(): array
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "utilisateur";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }


    public function findByMailandPWD(String  $mail, String $MDP): int
    {
        $sql = "SELECT count(idUtilisateur) FROM " . DB_PREFIX . "utilisateur WHERE mail = :mail AND mdp = :mdp";
        $pdo = $this->pdo->prepare($sql);
        $pdo->bindValue(':mail', $mail, PDO::PARAM_STR);
        $pdo->bindValue(':mdp', $MDP, PDO::PARAM_STR);
        $pdo->execute();
        return $pdo->fetch()["count(idUtilisateur)"];
    }

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
}
