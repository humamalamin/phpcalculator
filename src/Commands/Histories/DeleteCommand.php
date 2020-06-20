<?php

namespace Jakmall\Recruitment\Calculator\Commands\Histories;

use Illuminate\Console\Command;
use Jakmall\Recruitment\Calculator\Database\SQLiteConnection;
use Jakmall\Recruitment\Calculator\Repository\HistoryRepository;

class DeleteCommand extends Command
{
/**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'history:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear saved history';
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
        $this->processDatabase();
        $this->processedFile();
        $this->info('History cleared!');
    }

    protected function processDatabase()
    {
        $pdo = (new SQliteConnection())->connect();
        $sqlite = new HistoryRepository($pdo);
        $sqlite->delete();
    }

    protected function processedFile()
    {
        file_put_contents($this->urlFile, "");
    }
}
