<?php

namespace Jakmall\Recruitment\Calculator\Database;

class SQLiteCreateTable
{
    private $pdo;

    /**
     * connect to the SQLite database
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * create tables
     */
    public function createTables()
    {
        $commands = ['CREATE TABLE IF NOT EXISTS histories (
                        id INTEGER PRIMARY KEY,
                        command  VARCHAR (255) NOT NULL,
                        description VARCHAR(255) NOT NULL,
                        result INT,
                        output VARCHAR(255),
                        time timestamp)'
                    ];

        // execute the sql commands to create new tables
        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }
}
