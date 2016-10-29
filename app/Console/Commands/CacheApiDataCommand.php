<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Illuminate\Console\Command;
use App\Meetup;

class CacheApiDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:api {results=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache data from and API';

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
        $meetup = $this->meetupConnect();
        $results = $this->argument('results');

        $all_events = $meetup->getEvents([
            'group_urlname' => 'Memphis-PHP-Meetup',
            'page' => $results, // Results to return
        ]);

        $bar = $this->output->createProgressBar(count($all_events));
        foreach ($all_events as $event) {
            $event_date = Carbon::createFromTimestamp(
                $event['time'] / 1000
            );
            $meetup = $this->model->firstOrNew([
                'created' => $event['created'],
                'meetup_id' => $event['id'],
            ]);

            $meetup->meetup_id = $event['id'];
            $meetup->name = $event['name'];
            $meetup->time = $event['time'];
            $meetup->event_url = $event['event_url'];
            $meetup->description = $event['description'];
            $meetup->created = $event['created'];
            $meetup->venue_name = $event['venue']['name'];
            $meetup->venue_address_1 = $event['venue']['address_1'];
            $meetup->save();

            $this->info(' ' . $event['name'] . ' on ' . $event_date);
            $bar->advance();

            $events[] = [
                'name' => $event['name'],
                'date' => $event_date,
            ];
        }
        $bar->finish();

        $this->info('Found ' . count($all_events) . ' events');

        $headers = ['Name', 'Date'];

        $this->table($headers, $events);

    }

    /**
     *  Set up the Meetup API connection strings
     *  and create the connection.
     *
     * @return mixed
     */
    protected function meetupConnect()
    {
        $meetup_api_key = getenv('MEETUP_KEY');
        $connection = MeetupKeyAuthClient::factory([
            'key' => $meetup_api_key
        ]);

        return $connection;
    }
}
