<?php

namespace App\Console\Commands;

use Aws\Rds\RdsClient;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SnapshotRds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:rds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Snapshot of an RDS Database';

    /**
     * Our RDS client
     * @var static
     */
    protected $client;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->client = RdsClient::factory([
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
        $now = Carbon::now();
        $backup_identifier = 'db-' . $now->timestamp;

        $response = $this->client->createDBSnapshot([
            'DBSnapshotIdentifier' => $backup_identifier,
            'DBInstanceIdentifier' => 'consoleapps',
        ]);
        $result = $response->get('DBSnapshot');

        if ($result['Status'] == 'creating') {
            $this->info('Snapshot created');
        }

        if ($result['Status'] != 'creating') {
            $this->warn('Something went wrong');
        }
    }
}
