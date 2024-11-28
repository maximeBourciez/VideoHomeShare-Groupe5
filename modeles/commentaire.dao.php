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

    public function getMoyenneNoteContenu(string $idContenu): int {
        // SQL pour calculer la moyenne des notes
        $sql = 'SELECT AVG(note) AS moyenne FROM vhs_commenterContenu WHERE idContenu = :idContenu';


        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':idContenu', $idContenu, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && isset($result['moyenne'])) {
            return (int) round($result['moyenne']);
        }

        return 0;
    }

}