<?php  

class UtilisateurDAO {
    private $pdo;

    function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    function create(Utilisateur $utilisateur): bool{
        $req = $this->pdo->prepare("INSERT INTO utilisateur (pseudo, mail, mdp, role, urlImageProfil, urlImageBaniere) VALUES (:pseudo, :mail, :mdp, :role, :urlImageProfil, :urlImageBaniere)");
        $req->bindParam(":pseudo", $utilisateur->getPseudo());
        $req->bindParam(":mail", $utilisateur->getMail());
        $req->bindParam(":mdp", $utilisateur->getMdp());
        $req->bindParam(":role", $utilisateur->getRole());
        $req->bindParam(":urlImageProfil", $utilisateur->getUrlImageProfil());
        $req->bindParam(":urlImageBaniere", $utilisateur->getUrlImageBaniere());
        return $req->execute();
    }

    function find(int $id): Utilisateur{
        $req = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $req->bindParam(":id", $id);
        $req->execute();
        $data = $req->fetch();
        return new Utilisateur($data["id"], $data["pseudo"], $data["mail"], $data["mdp"], $data["role"], $data["urlImageProfil"], $data["urlImageBaniere"]);
    }

    function update(Utilisateur $utilisateur): bool{
        $req = $this->pdo->prepare("UPDATE utilisateur SET pseudo = :pseudo, mail = :mail, mdp = :mdp, role = :role, urlImageProfil = :urlImageProfil, urlImageBaniere = :urlImageBaniere WHERE id = :id");
        $req->bindParam(":id", $utilisateur->getId());
        $req->bindParam(":pseudo", $utilisateur->getPseudo());
        $req->bindParam(":mail", $utilisateur->getMail());
        $req->bindParam(":mdp", $utilisateur->getMdp());
        $req->bindParam(":role", $utilisateur->getRole());
        $req->bindParam(":urlImageProfil", $utilisateur->getUrlImageProfil());
        $req->bindParam(":urlImageBaniere", $utilisateur->getUrlImageBaniere());
        return $req->execute();
    }

    function delete(int $id): bool{
        $req = $this->pdo->prepare("DELETE FROM utilisateur WHERE id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }

    function findAll(): array{
        $req = $this->pdo->prepare("SELECT * FROM utilisateur");
        $req->execute();
        $data = $req->fetchAll();
        $utilisateurs = [];
        foreach($data as $d){
            $utilisateurs[] = new Utilisateur($d["id"], $d["pseudo"], $d["mail"], $d["mdp"], $d["role"], $d["urlImageProfil"], $d["urlImageBaniere"]);
        }
        return $utilisateurs;
    }
}