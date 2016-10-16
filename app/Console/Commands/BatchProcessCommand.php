<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Meetup;

class BatchProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetup:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove old meetups from the database';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new Meetup;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $meetups = $this->model->all();
        $today = new Carbon('now', 'UTC');

        foreach($meetups as $meetup)
        {
            $event_date = Carbon::createFromTimestamp(
                $meetup['time'] / 1000
            );

            if($today->gt($event_date)) {
                $this->info('Deleting ' . $meetup['name'] . ' on ' . $event_date);
            }
        }

        $this->info('Finished expiring old meetups');
    }
}
