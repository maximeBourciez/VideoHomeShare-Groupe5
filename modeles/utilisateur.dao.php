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

    function hydrate(array $row): Utilisateur{
        // Récupération des valeurs
        $id = $row['id'];
        $pseudo = $row['pseudo'];
        $mail = $row['mail'];
        $mdp = $row['mdp'];
        $role = $row['role'];
        $urlImageProfil = $row['urlImageProfil'];
        $urlImageBaniere = $row['urlImageBaniere'];

        // Retourner l'utilisateur
        return new Utilisateur($id, $pseudo, $mail, $mdp, $role, $urlImageProfil, $urlImageBaniere);
    }

    function hydrateAll(array $rows): array{
        $utilisateurs = [];
        foreach($rows as $row){
            $utilisateur = $this->hydrate($row);
            array_push($utilisateurs, $utilisateur);  // Ajout de l'utilisateur au tableau 
        }
        return $utilisateurs;
    }

    function find(int $id): ?Utilisateur{
        $sql = "SELECT * FROM utilisateur WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $row = $stmt->fetch();
        if($row){
            return $this->hydrate($row);
        }
        return null;
    }

    function findAll(): array{
        $sql = "SELECT * FROM utilisateur";
        $stmt = $this->pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        return $this->hydrateAll($stmt->fetchAll());
    }

}