<?php

namespace App\Console\Commands;

use App\Http\directions\borderCrossings\Dto\DirectionCrossingDTO;
use App\Http\directions\borderCrossings\Services\BorderCrossingService;
use Illuminate\Console\Command;

class FetchPolandBorderData extends Command
{
    protected $signature = 'fetch:poland-border-data';

    protected $description = 'Fetch data from Poland border crossings and store in cache';

    private $borderDataService;

    public function __construct(BorderCrossingService $borderDataService)
    {
        parent::__construct();
        $this->borderDataService = $borderDataService;
    }

    public function handle()
    {
        $this->borderDataService->fetchDataFromPolandApi();

        $this->info('Data fetched successfully.');
    }
}
