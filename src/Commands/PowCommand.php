<?php

namespace Jakmall\Recruitment\Calculator\Commands;

use Illuminate\Console\Command;

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
            $description = $base. ' ^ '. $exp;
            $summary = $this->summaryAll($base, $exp);
            $result = strval($description).' = '.strval($summary);
        } else {
            $this->info('Please fill your base and exponent number');
            exit;
        }

        return $result;
    }

    protected function summaryAll($base, $exp)
    {
        return pow($base, $exp);
    }
}
