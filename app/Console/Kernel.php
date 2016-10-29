<?php

namespace App\Console;

use App\Console\Commands\ArgumentExampleCommand;
use App\Console\Commands\BasicCommand;
use App\Console\Commands\BatchProcessCommand;
use App\Console\Commands\CacheApiDataCommand;
use App\Console\Commands\DatabaseBackupCommand;
use App\Console\Commands\GetAllMeetupsCommand;
use App\Console\Commands\OptionExampleCommand;
use App\Console\Commands\PromptExampleCommand;
use App\Console\Commands\S3ExampleCommand;
use App\Console\Commands\SnapshotRds;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BasicCommand::class,
        DatabaseBackupCommand::class,
        SnapshotRds::class,
        S3ExampleCommand::class,
        CacheApiDataCommand::class,
        BatchProcessCommand::class,
        GetAllMeetupsCommand::class,
        ArgumentExampleCommand::class,
        OptionExampleCommand::class,
        PromptExampleCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Snapshot RDS At midnight every night
        $schedule->command('backup:rds')->daily();

        // Refresh our Meetup DB every 30 minutes
        $schedule->command('meetup:pull')->everyThirtyMinutes();

        // Expire old Meet ups daily at 8:00 AM
        $schedule->command('meetup:expire')->dailyAt('08:00');

        // Back up our DB on the 1st of the month @ 1:00 AM
        $schedule->command('meetup:expire')->monthlyOn(1, '01:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
