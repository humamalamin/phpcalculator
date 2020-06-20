<?php

namespace Jakmall\Recruitment\Calculator\Database;

class SQLiteConnection
{
    private $pdo;

    /**
     * return in instance of the PDO object that connects to the SQLite database
     * @return \PDO
     */
    public function connect()
    {
        try {
            $this->pdo = new \PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);

            return $this->pdo;
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }
}
