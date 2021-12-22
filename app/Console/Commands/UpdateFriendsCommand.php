<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Friend;
use App\Models\Girl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use VK\Client\VKApiClient;

class UpdateFriendsCommand extends Command
{
    public $application;
    public $girls;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'friends:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить список друзей';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->application = Application::where('worked', 0)->first();
        $this->girls = Girl::all();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->application->worked = 1;
        $this->application->save();
        $count = $this->girls->count();

        $vk = new VKApiClient();
        echo 'Найдено '.$count.PHP_EOL;
        $i = 1;
        foreach ($this->girls as $girl) {
            echo 'Обработка '.$i.'/'.$count.PHP_EOL;
            ++$i;
            $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
            $girl_id = str_replace($removeChar, "", $girl->url);

            try {
                $first_action = microtime(true);
                $getListFriends = $vk->friends()->get($this->application->access_token, array(
                    'user_id' => $girl_id,
                    'order' => 'name',
                    'fields' => 'city,sex,bdate,last_seen,photo_200_orig',
                ));
                foreach ($getListFriends as $friend) {
                    if (!isset($friend['city'])) {
                        continue;
                    }
                    if ($friend['sex'] == 1 and $friend['city']['id'] == 650) {

                        $new_friend = Friend::firstOrCreate(
                            ['url' => 'https://vk.com/id' . $friend['id']],
                            [
                                'first_name' => $friend['first_name'],
                                'last_name' => $friend['last_name'],
                                'bdate' => $friend['bdate'],
                                'last_seen' => $friend['last_seen']['time'],
                                'photo' => '---',
                                'url_photo' => $friend['photo_200_orig'],
                                'instagram' => '---',
                            ]
                        );
                        dump($new_friend);
                        if (isset($friend['photo_200_orig'])) {
                            try {
                                Storage::disk('public')->put('friends/'.$new_friend->id.'_photo.jpg', file_get_contents($friend['photo_200_orig']));
                                $new_friend->photo = 'storage/friends/'.$new_friend->id.'_photo.jpg';
                                $new_friend->save();
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }
                }
                $last_action = microtime(true);
                $time_difference = $last_action - $first_action;
                if ($time_difference >= 0.22) {
                    continue;
                }
                usleep(220000 - $time_difference);
            } catch (\Exception $exception) {
                continue;
            }
        }
        $this->application->worked = 0;
        $this->application->save();
    }
}
