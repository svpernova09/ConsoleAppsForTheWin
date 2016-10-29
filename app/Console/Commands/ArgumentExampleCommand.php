<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ArgumentExampleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'input:example 
                                {first : First Argument}
                                {second : Second Argument}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Input with 1 Argument';

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
        $first = $this->argument('first');
        $second = $this->argument('second');

        $this->info('First argument: ' . $first);
        $this->info('Second argument: ' . $second);

        $args = $this->arguments();

        $this->info('First argument: ' . $args['first']);
        $this->info('Second argument: ' . $args['second']);
    }
}
