<?php

namespace classes;
require_once __DIR__ . '/../db/config.php';

use PDO;
use PDOException;

class QnA
{
    private $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $config = DATABASE;

        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        );
        try {
            $this->conn = new PDO('mysql:host=' . $config['HOST'] . ';dbname=' .
                $config['DBNAME'] . ';port=' . $config['PORT'], $config['USER_NAME'],
                $config['PASSWORD'], $options);
        } catch (PDOException $e) {
            die("Chyba pripojenia: " . $e->getMessage());
        }
    }

    public function getQuestions()
    {
        $statement = $this->conn->query("SELECT question, answer FROM qna");
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertQnA()
    {
        try {
            // Načítanie JSON súboru
            $data = json_decode(file_get_contents(__ROOT__ . '/data/datas.json'), true);
            $otazky = $data["otazky"];
            $odpovede = $data["odpovede"];

            // Vloženie otázok a odpovedí v rámci transakcie
            $this->conn->beginTransaction();

//            Bonusová úloha:

            $sql = "SELECT COUNT(*) FROM qna WHERE question = :question AND answer = :answer";
            $statement = $this->conn->prepare($sql);

            for ($i = 0; $i < count($otazky); $i++) {
                $statement->bindParam(':question', $otazky[$i]);
                $statement->bindParam(':answer', $odpovede[$i]);
//                get an amount of qnas
                $statement->execute();


//                if there is no such a qna - create
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

?>
