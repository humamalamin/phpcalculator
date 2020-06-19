<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;

class SubtractCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subtract {numbers* : The numbers to be subtract}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subtract all given Numbers';

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
        $numbers = $this->argument('numbers');
        if (count($numbers) > 0) {
            $description = implode(' - ', $numbers);
            $summary = $this->summaryAll($numbers);
            $result = strval($description).' = '.strval($summary);
        } else {
            $this->info('Please fill your numbers');
            exit;
        }

        return $result;
    }

    protected function summaryAll(array $numbers)
    {
        $result = 0;
        if (count($numbers) > 0) {
            foreach ($numbers as $key => $value) {
                if ($key == 0) {
                    $result = $value;
                } else {
                    $result = $result - $value;
                }
            }
        }

        return $result;
    }
}
