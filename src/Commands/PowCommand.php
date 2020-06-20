<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\Database\SQLiteConnection;
use Jakmall\Recruitment\Calculator\Repository\HistoryRepository;

class PowCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pow {base : The base number} {exp : The exponent number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exponent the given number';
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
        $base = $this->argument('base');
        $exp = $this->argument('exp');

        if (!empty($base) && !empty($exp)) {
            $description = $this->generateCommand($base, $exp);
            $summary = $this->summaryAll($base, $exp);
            $result = strval($description).' = '.strval($summary);
            $this->processDatabase($description, $summary, $result);
            $this->processFile($description, $summary, $result);
        } else {
            $this->info('Please fill your base and exponent number');
            exit;
        }

        return $result;
    }

    protected function generateCommand($base, $exp)
    {
        return $base.' ^ '. $exp;
    }

    protected function summaryAll($base, $exp)
    {
        return pow($base, $exp);
    }

    protected function getVerb()
    {
        return "Pow";
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
