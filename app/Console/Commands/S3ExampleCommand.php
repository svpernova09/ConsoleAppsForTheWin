<?php

namespace App\Console\Commands;

use Aws\S3\S3Client;
use Carbon\Carbon;
use Illuminate\Console\Command;

class S3ExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:example';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'S3 Example Command';

    /**
     * Our S3 client
     * @var static
     */
    protected $client;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = S3Client::factory([
            'version'     => 'latest',
            'region'      => 'us-east-1',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $response = $this->client->listObjects([
            'Bucket' => 'consoleapps',
        ]);
        $results = $response->get('Contents');

        foreach($results as $item)
        {
            $last_month = Carbon::now()->subMonth(1);
            $last_modified = Carbon::instance($item['LastModified']);

            // Is the file a week old or more?
            if ($last_month->gte($last_modified)) {
                $this->info('Deleting ' . $item['Key'] . ' since it is over a month old');
                $this->client->deleteObject([
                    'Bucket' => 'consoleapps',
                    'Key' => $item['Key'],
                ]);
            }
        }
    }
}
