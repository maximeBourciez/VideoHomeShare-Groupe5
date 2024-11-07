<?php  

class CateforieDAO {
    private $pdo;

    function __construct(?PDO $pdo = null){
        $this->pdo = $pdo;
    }

    function findAll(): array{
        $sql = "SELECT * FROM categorie";
        $stmt = $this->pdo->query($sql);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tab = [];
        foreach($res as $ligne){
            $tab[] = new Categorie($ligne['id'], $ligne['nom']);
        }
        return $tab;
    }

    function find(int $id): Categorie{
        $sql = "SELECT * FROM categorie WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return new Categorie($res['id'], $res['nom']);
    }

    function insert(Categorie $c): void{
        $sql = "INSERT INTO categorie (nom) VALUES (?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$c->getNom()]);
    }

    function update(Categorie $c): void{
        $sql = "UPDATE categorie SET nom = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$c->getNom(), $c->getId()]);
    }

    function delete(int $id): void{
        $sql = "DELETE FROM categorie WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
    }
}