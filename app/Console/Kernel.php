<?php

namespace App\Console;

use App\Console\Commands\FixFriendListCommand;
use App\Console\Commands\RenamePhotoCommand;
use App\Console\Commands\UpdateFriendsCommand;
use App\Console\Commands\UpdateGroupsCommand;
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
        UpdateGroupsCommand::class,
        UpdateFriendsCommand::class,
//        FixFriendListCommand::class,
        RenamePhotoCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
