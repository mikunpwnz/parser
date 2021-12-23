<?php

namespace App\Console\Commands;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Console\Command;

class FixFriendListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friends:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Убрать ненужных пользователей';

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
    public function handle()
    {
        $friends = Friend::where('wrote', 0)
            ->where('need_to_write', 0)
            ->get();
        foreach ($friends as $friend) {
            if (strlen($friends->bdate) >= 8) {
                $array = explode(".", $friends->bdate);
                $year = $array[2];
                if ($year < 1990) {
                    $friend->wrote = 1;
                    $friend->save();
                }
            }

//            if($friend->last_seen < 1609473660) {
//                $friend->wrote = 1;
//                $friend->save();
//                continue;
//            }

        }


    }
}
