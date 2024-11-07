<?php

class PersonaliteDAO {
    // Attributs
    private $pdo;

    // Constructeur
    function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    // Méthodes

    function create(Personalite $personalite): bool{
        $req = $this->pdo->prepare("INSERT INTO personalite (nom, prenom, urlImage) VALUES (:nom, :prenom, :urlImage)");
        $req->bindParam(":nom", $personalite->getNom());
        $req->bindParam(":prenom", $personalite->getPrenom());
        $req->bindParam(":urlImage", $personalite->getUrlImage());
        return $req->execute();
    }

    function find(int $id): Personalite{
        $req = $this->pdo->prepare("SELECT * FROM personalite WHERE id = :id");
        $req->bindParam(":id", $id);
        $req->execute();
        $data = $req->fetch();
        return new Personalite($data["id"], $data["nom"], $data["prenom"], $data["urlImage"]);
    }

    function update(Personalite $personalite): bool{
        $req = $this->pdo->prepare("UPDATE personalite SET nom = :nom, prenom = :prenom, urlImage = :urlImage WHERE id = :id");
        $req->bindParam(":id", $personalite->getId());
        $req->bindParam(":nom", $personalite->getNom());
        $req->bindParam(":prenom", $personalite->getPrenom());
        $req->bindParam(":urlImage", $personalite->getUrlImage());
        return $req->execute();
    }

    function delete(int $id): bool{
        $req = $this->pdo->prepare("DELETE FROM personalite WHERE id = :id");
        $req->bindParam(":id", $id);
        return $req->execute();
    }

    function findAll(): array{
        $req = $this->pdo->prepare("SELECT * FROM personalite");
        $req->execute();
        $data = $req->fetchAll();
        $personalites = [];
        foreach($data as $d){
            $personalites[] = new Personalite($d["id"], $d["nom"], $d["prenom"], $d["urlImage"]);
        }
        return $personalites;
    }
    
}