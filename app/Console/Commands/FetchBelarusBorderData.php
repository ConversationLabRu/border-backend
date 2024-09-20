<?php

namespace App\Console\Commands;

use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Console\Command;

class FetchBelarusBorderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:belarus-border-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $borderDataService;

    public function __construct(BorderCrossingService $borderDataService)
    {
        parent::__construct();
        $this->borderDataService = $borderDataService;
    }

    public function handle()
    {
        $this->borderDataService->fetchDataFromExternalApi();

        $this->info('Data fetched successfully.');
    }
}
