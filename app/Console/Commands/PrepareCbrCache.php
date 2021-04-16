<?php

namespace App\Console\Commands;

use App\Services\CbrRuService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PrepareCbrCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cbr:prepare-cache {fromDate?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Precache rates from cbr.ru';

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
     * @return int
     */
    public function handle(CbrRuService $cbrRuService)
    {
        $fromDate = $this->argument('fromDate') ?? '2021-01-01';
        $carbonFromDate = Carbon::createFromFormat('Y-m-d', $fromDate);
        $date = now();
        while($date->greaterThan($fromDate)){
            $this->info('precache ' . $date->toDateString());
            $cbrRuService->fetchByDay($date->toDateString());
            $date->sub('1 day');
        }
        return 0;
    }
}
