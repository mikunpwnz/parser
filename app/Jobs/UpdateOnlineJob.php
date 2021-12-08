<?php

namespace App\Jobs;

use App\Models\Application;
use App\Models\Girl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use VK\Client\VKApiClient;

class UpdateOnlineJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 36000;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $application = Application::where('worked', 0)->first();
        $access_token = $application->access_token;
        $application->worked = 1;
        $application->save();

        $girls = Girl::all();
        $girls_count = Girl::all()->count();
        $count = ceil($girls_count/1000);

        $vk = new VKApiClient();

        $offset = 0;
        $counter = 0;

        for ($i = 0; $i < $count; ++$i) {
            $girl_list = $girls->slice($offset, 1000);
            $profilesId = [];
            foreach($girl_list as $girl) {
                $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
                $girl_id = str_replace($removeChar, "", $girl->url);
                $profilesId[] = $girl_id;
            }

            $getInfoUser = $vk->users()->get($access_token, array(
                'user_ids' => $profilesId,
                'fields' => 'photo_200,last_seen, connections'
            ));
            foreach ($getInfoUser as $user) {
                if (isset($user['last_seen']['time'])) {
                    $girl_new = Girl::where('url', 'like', '%'.$user['id'])->first();
                    $girl_new->last_seen = $user['last_seen']['time'];
                    if ($user['photo_200'] !== $girl_new->url_photo) {
                        Storage::disk('public')->put($girl_new->id.'_photo.jpg', file_get_contents($user['photo_200']));
                        $girl_new->photo = 'storage/'.$girl_new->id.'_photo.jpg';
                        $girl_new->url_photo = $user['photo_200'];
                    }
                    if(isset($user['instagram'])) {
                        $girl_new->instagram = 'https://instagram.com/'.$user['instagram'];
                    }
                    $girl_new->save();
                }
                ++$counter;
            }
            $offset += 1000;
        }

        $application->worked = 0;
        $application->save();
    }
}
