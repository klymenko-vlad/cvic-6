<?php

namespace Classes;

require_once __DIR__ . '/../db/Database.php';

use Database\Database;
use PDO;
use Exception;

class QnA
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getQuestions()
    {
        $statement = $this->conn->query("SELECT question, answer FROM qna");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertQnA()
    {
        try {
            $data = json_decode(file_get_contents(__ROOT__ . '/data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            $this->conn->beginTransaction();

            $sql = "SELECT COUNT(*) FROM qna WHERE question = :question AND answer = :answer";
            $statement = $this->conn->prepare($sql);

            for ($i = 0; $i < count($otazky); $i++) {
                $statement->bindParam(':question', $otazky[$i]);
                $statement->bindParam(':answer', $odpovede[$i]);
                $statement->execute();

                if ($statement->fetchColumn() == 0) {
                    $insertSql = "INSERT INTO qna (question, answer) VALUES (:question, :answer)";
                    $insertStatement = $this->conn->prepare($insertSql);
                    $insertStatement->bindParam(':question', $otazky[$i]);
                    $insertStatement->bindParam(':answer', $odpovede[$i]);
                    $insertStatement->execute();
                }
            }

            $this->conn->commit();
            echo "Dáta boli vložené";
        } catch (Exception $e) {
            $this->conn->rollBack();
            echo "Chyba pri vkladaní dát: " . $e->getMessage();
        }
    }
}
