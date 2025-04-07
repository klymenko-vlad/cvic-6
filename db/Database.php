<?php

namespace Database;

use PDO;
use PDOException;

class Database
{
    protected $conn;

    const DATABASE = [
        'HOST' => 'localhost',
        'DBNAME' => 'sablona',
        'PORT' => 3306,
        'USER_NAME' => 'root',
        'PASSWORD' => '',
    ];

    public function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $config = self::DATABASE;

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->conn = new PDO(
                'mysql:host=' . $config['HOST'] . ';dbname=' . $config['DBNAME'] . ';port=' . $config['PORT'],
                $config['USER_NAME'],
                $config['PASSWORD'],
                $options
            );
        } catch (PDOException $e) {
            die("Connection error: " . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}
