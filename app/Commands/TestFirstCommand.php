<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class TestFirstCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'app:test-first {first} {last}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Look up a first and last name';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): void
    {

        $first=$this->argument('first');
        $last=$this->argument('last');

        dump(Worker::NameLookUp($first, $last));

    }


}
