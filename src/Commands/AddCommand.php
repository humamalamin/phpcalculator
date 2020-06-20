<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\Database\SQLiteConnection;
use Jakmall\Recruitment\Calculator\Repository\HistoryRepository;

class AddCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add {numbers* : The numbers to be added}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add all given Numbers';
    protected $storage = [];

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
        $result = $this->processCalculate();
        $this->line($result."\n");
    }

    protected function processCalculate()
    {
        $numbers = $this->getInput();
        if (count($numbers) > 0) {
            $description = $this->generateCommand($numbers);
            $summary = $this->summaryAll($numbers);
            $result = strval($description).' = '.strval($summary);
            $this->processFile($description, $summary, $result);
            $this->processDatabase($description, $summary, $result);
        } else {
            $this->info('Please fill your numbers');
            exit;
        }

        return $result;
    }

    protected function getInput()
    {
        return $this->argument('numbers');
    }

    protected function generateCommand($array)
    {
        return implode(' + ', $array);
    }

    protected function summaryAll(array $numbers)
    {
        $result = 0;
        if (count($numbers) > 0) {
            $result = array_sum($numbers);
        }

        return $result;
    }

    protected function getVerb()
    {
        return "Add";
    }

    /**
     * Process save database
     */
    protected function processDatabase($description, $summary, $result)
    {
        $pdo = (new SQliteConnection())->connect();
        $sqlite = new HistoryRepository($pdo);
        $sqlite->insert($this->getVerb(), $description, $summary, $result);
    }

    /**
     * Process save in file
     */
    protected function processFile($description, $result, $output)
    {
        $date = date('Y-m-d H:i:s');
        $this->storage = [
            'command' => $this->getVerb(),
            'description' => $description,
            'result' => $result,
            'output' => $output,
            'time' => $date
        ];

        $filePath = fopen('src/history.txt', 'a');
        $content = $this->storage['command'].';'.$this->storage['description'].';'.$this->storage['result'].';'.$this->storage['output'].';'.$this->storage['time'];
        fwrite($filePath, $content. "\n");
        fclose($filePath);
    }
}
