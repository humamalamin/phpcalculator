<?php

namespace Jakmall\Recruitment\Calculator\Commands\Histories;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\Database\SQLiteConnection;
use Jakmall\Recruitment\Calculator\Repository\HistoryRepository;

class ListCommand extends Command
{
/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'history:list {--driver= : Driver for storage connection default: "database"} {--commands=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show calculator history';
    protected $urlFile = "src/history.txt";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->validateFile();
        $this->createHeader();
        if (!empty($this->option('driver'))) {
            if ($this->option('driver') === "database") {
                $this->processDatabase();
            } else {
                $this->processedFile();
            }
        } else {
            $this->processDatabase();
        }
    }

    protected function validateFile()
    {
        if (!file_exists($this->urlFile)) {
            $this->info('History is empty.');
            exit;
        } else {
            $read = file($this->urlFile);
            if (!count($read)) {
                $this->info('History is empty.');
                exit;
            }
        }
    }

    protected function processDatabase()
    {
        $pdo = (new SQliteConnection())->connect();
        $sqlite = new HistoryRepository($pdo);
        $commands = array_map('ucfirst', $this->option('commands'));
        $results = $sqlite->index();

        $contentArr = [];
        if (empty($commands)) {
            $results = $sqlite->index();
        } else {
            $results = $sqlite->show($commands);
        }

        $index = 1;
        foreach ($results as $result) {
            array_push($contentArr, [
                "no" => $index,
                "command" => $result['command'],
                "description" => $result['description'],
                "result" => $result['result'],
                "output" => $result['output'],
                "time" => $result['time'],
            ]);
            $index++;
        }

        $this->filledContent($contentArr);
    }

    protected function processedFile()
    {
        $contentArr = [];

        if (file_exists($this->urlFile)) {
            $reads = file($this->urlFile);
            $index = 1;

            if (count($this->option('commands'))) {
                $this->mappingData($this->option('commands'), $reads);
            } else {
                foreach ($reads as $read) {
                    $arrExplode = explode(';', $read);
                    array_push($contentArr, [
                        "no" => $index,
                        "command" => $arrExplode[0],
                        "description" => $arrExplode[1],
                        "result" => $arrExplode[2],
                        "output" => $arrExplode[3],
                        "time" => $arrExplode[4],
                    ]);
                    $index++;
                }

                $this->filledContent($contentArr);
            }
        }
    }

    protected function mappingData(array $commands, $files)
    {
        $listCommands = ["add", "subtract", "devide", "multiply", "pow"];
        $commands = $this->option('commands');
        $arrayContents = [];
        $index = 1;
        foreach ($files as $file) {
            $arrayFiles = explode(';', $file);
            foreach ($commands as $command) {
                if (in_array($command, $listCommands)) {
                    if ($arrayFiles[0] == ucfirst($command)) {
                        array_push($arrayContents, [
                            "no" => $index,
                            "command" => $arrayFiles[0],
                            "description" => $arrayFiles[1],
                            "result" => $arrayFiles[2],
                            "output" => $arrayFiles[3],
                            "time" => $arrayFiles[4],
                        ]);

                        $index++;
                    }
                }
            }
        }

        return $this->filledContent($arrayContents);
    }

    protected function filledContent($contentArr)
    {
        $headers = ['No', 'Command', 'Description', 'Result', 'Output', 'Time'];
        $this->table($headers, $contentArr);
    }
}
