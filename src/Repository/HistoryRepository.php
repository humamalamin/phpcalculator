<?php

namespace Jakmall\Recruitment\Calculator\Repository;

use Jakmall\Recruitment\Calculator\Database\SQLiteCreateTable;

class HistoryRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;

        $sqlite = new SQLiteCreateTable($this->pdo);
        // create new tables
        $sqlite->createTables();
    }

    public function index()
    {
        $stmt = $this->pdo->query('SELECT * FROM histories');
        return $stmt->fetchAll();
    }

    public function show($command)
    {
        $questionmarks = str_repeat("?,", count($command)-1) . "?";
        $sql = "SELECT * FROM `histories` WHERE `command` in ($questionmarks)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($command);

        $histories = [];
        while ($history = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $histories[] = [
                'command' => $history['command'],
                'description' => $history['description'],
                'result' => $history['result'],
                'output' => $history['output'],
                'time' => $history['time'],
            ];
        }

        return $histories;
    }

    public function insert($command, $description, $result, $output)
    {
        $time = date('Y-m-d H:i:s');
        $sql = 'INSERT INTO histories(command, description, result, output, time) VALUES(:command, :description, :result, :output, :time)';
        $statement = $this->pdo->prepare($sql);
        $statement->execute([
                ':command' => $command,
                ':description' => $description,
                ':result' => $result,
                ':output' => $output,
                ':time' => $time
        ]);
    }
}
