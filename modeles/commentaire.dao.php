<?php
class commentaireDAO{

    private ?PDO $pdo;

    public function __construct(?PDO $pdo=null){
        $this->pdo = $pdo;
    }

    //get pdo
    public function getPdo(): ?PDO{
        return $this->pdo;
    }

    // set pdo
    public function setPdo(PDO $pdo){
        $this->pdo = $pdo;
    }

public function getMoyenneEtTotalNotesContenu(string $idContenu): array {
    // SQL pour calculer la moyenne et le total des notes
    $sql = 'SELECT AVG(note) AS moyenne, COUNT(note) AS total 
            FROM vhs_commenterContenu 
            WHERE idContenu = :idContenu';

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':idContenu', $idContenu, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return [
            'moyenne' => isset($result['moyenne']) ? (int) round($result['moyenne']) : 0,
            'total' => isset($result['total']) ? (int) $result['total'] : 0,
        ];
    }

    // Si aucun résultat n'est trouvé, retourner des valeurs par défaut
    return [
        'moyenne' => 0,
        'total' => 0,
        ];
    }
    public function hydrate(array $tableauAssaus): Commentaire{
        return new Commentaire($tableauAssaus['idUtilisateur'], $tableauAssaus['titre'], $tableauAssaus['note'], $tableauAssaus['avis'],$tableauAssaus['estPositif']);
    }

    public function hydrateAll(array $tableauAssaus): ?array{
        $commentaires = [];
        foreach ($tableauAssaus as $row){
            $commentaires[] = $this->hydrate($row);
        }
        return $commentaires;
    }
}