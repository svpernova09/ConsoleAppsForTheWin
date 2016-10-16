<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Illuminate\Console\Command;
use App\Meetup;

class GetAllMeetupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetup:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull Large Set of Meetups';

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
        $m = $this->meetupConnect();
        $all_events = $m->getEvents([
            'group_urlname' => 'memphis-technology-user-groups',
        ]);

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
            $meetup->description = 'no description';
            if (array_key_exists('description', $event)) {
                $meetup->description = $event['description'];
            }

            $meetup->venue_name = 'no venue set';
            $meetup->venue_address_1 = 'no venue address';
            $meetup->created = $event['created'];
            if (array_key_exists('venue', $event)) {
                $meetup->venue_name = $event['venue']['name'];
                $meetup->venue_address_1 = $event['venue']['address_1'];
            }

            $meetup->save();
            $this->info($event['name'] . ' on ' . $event_date);
        }
    }

    /**
     *  Set up the Meetup API connection strings and create the connection.
     *
     *  @return mixed
     */
    protected function meetupConnect()
    {
        $meetup_api_key = getenv('MEETUP_KEY');
        $connection = MeetupKeyAuthClient::factory(array('key' => $meetup_api_key));

        return $connection;
    }
}
