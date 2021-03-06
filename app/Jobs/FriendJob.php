<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\Friend;
use App\Models\Girl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use VK\Client\VKApiClient;

class FriendJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $application;
    public $girls;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->application = Application::where('worked', 0)->first();
        $this->girls = Girl::first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->application->worked = 1;
        $this->application->save();

        $vk = new VKApiClient();

        foreach ($this->girls as $girl) {
            $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
            $girl_id = str_replace($removeChar, "", $girl->url);

            try {
                $getListFriends = $vk->friends()->get($this->application->access_token, array(
                    'user_id' => $girl_id,
                    'order' => 'name',
                    'fields' => 'city,sex,bdate,last_seen,photo_200_orig',
                ));
                foreach ($getListFriends as $friend) {
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
                        if (isset($friend['photo_200_orig'])) {
                            try {
                                Storage::disk('public')->put('friends/'.$new_friend->id.'_photo.jpg', file_get_contents($friend['photo_200_orig']));
                                $new_friend->photo = 'storage/friends'.$new_friend->id.'_photo.jpg';
                                $new_friend->save();
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                    }
                }
            } catch (\Exception $exception) {
                continue;
            }
        }

        $this->application->worked = 0;
        $this->application->save();
    }
}
