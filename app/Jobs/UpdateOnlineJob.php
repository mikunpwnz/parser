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
use PHPUnit\Exception;
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

//        $girls = Girl::all();
//        $girls_count = Girl::all()->count();
//        $count = ceil($girls_count/1000);
//
//        $vk = new VKApiClient();
//
//        $offset = 0;
//        $counter = 0;
//
//        for ($i = 0; $i < $count; ++$i) {
//            $girl_list = $girls->slice($offset, 1000);
//            $profilesId = [];
//            foreach($girl_list as $girl) {
//                $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
//                $girl_id = str_replace($removeChar, "", $girl->url);
//                $profilesId[] = $girl_id;
//            }
//
//            $getInfoUser = $vk->users()->get($access_token, array(
//                'user_ids' => $profilesId,
//                'fields' => 'photo_200,last_seen, connections'
//            ));
//            foreach ($getInfoUser as $user) {
//                if (isset($user['last_seen']['time'])) {
//                    $girl_new = Girl::where('url', 'like', '%'.$user['id'])->first();
//                    $girl_new->last_seen = $user['last_seen']['time'];
//                    if ($girl_new->url_photo !== $user['photo_200']) {
//                        try {
//                            Storage::disk('public')->put($girl_new->id.'_photo.jpg', file_get_contents($user['photo_200']));
//                            $girl_new->photo = 'storage/'.$girl_new->id.'_photo.jpg';
//                            $girl_new->url_photo = $user['photo_200'];
//                        } catch (\Exception $e) {
//                            continue;
//                        }
//                    }
////                    $girl_new->url_photo = $user['photo_200'];
//                    if(isset($user['instagram'])) {
//                        $girl_new->instagram = 'https://instagram.com/'.$user['instagram'];
//                    }
//                    else
//                    {
//                        $girl_new->instagram = '---';
//                    }
//                    $girl_new->save();
//                }
//                ++$counter;
//            }
//            $offset += 1000;
//        }

        $vk = new VKApiClient();

//        $offset = 0;
//        $counter = 0;

        Friend::chunkById(1000, function($friends) use ($access_token, $vk) {
            $profilesId = [];
            foreach ($friends as $friend) {
                $profilesId[] = $friend->vk_id;
            }
            dump($profilesId);
            $getInfoUser = $vk->users()->get($access_token, array(
                'user_ids' => $profilesId,
                'fields' => 'photo_200,last_seen, connections'
            ));

            $query = [];
            foreach ($getInfoUser as $user) {
                if (isset($user['last_seen']['time'])) {
                    $query[]= [
                        'url'        => 'https://vk.com/id'.$user['id'],
                        'first_name' => $user['first_name'],
                        'last_name'  => $user['last_name'],
                        'bdate'      => (isset($user['bdate'])) ? $user['bdate'] : '---',
                        'photo'      => 'storage/friends/'.$user['id'].'_photo.jpg',
                        'vk_id'      => $user['id'],
                        'last_seen'  => $user['last_seen']['time'],
                        'url_photo'  => $user['photo_200'],
                        'instagram'  => (isset($user['instagram'])) ? 'https://instagram.com/' . $user['instagram'] : '---',
                    ];
                    try {
                        Storage::disk('public')->put('friends/'.$user['id'].'_photo.jpg', file_get_contents($user['photo_200']));
                    } catch (Exception $exception) {
                        continue;
                    }
                }
            }
            Friend::upsert($query, ['vk_id'], ['last_seen', 'url_photo']);
        });

//        $girls = Girl::all();
//        $girls_count = Girl::all()->count();
//        $count = ceil($girls_count/1000);

//
//
//        $girls = Friend::all();
//        $girls_count = Friend::all()->count();
//        $count = ceil($girls_count/1000);
//
//        $vk = new VKApiClient();
//
//        $offset = 0;
//        $counter = 0;
//
//        for ($i = 0; $i < $count; ++$i) {
//            echo 'ЦИКЛ '.$i; ##########################################
//            $girl_list = $girls->slice($offset, 1000);
//            $profilesId = [];
//            foreach($girl_list as $girl) {
//                $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
//                $girl_id = str_replace($removeChar, "", $girl->url);
//                $profilesId[] = $girl_id;
//            }
//
//            $getInfoUser = $vk->users()->get($access_token, array(
//                'user_ids' => $profilesId,
//                'fields' => 'photo_200,last_seen, connections'
//            ));
//            foreach ($getInfoUser as $key=>$user) {
//                if (isset($user['last_seen']['time'])) {
//                    echo 'ЗАПУСК '.$key;
//                    $girl_new = Friend::where('url', 'like', '%'.$user['id'])->first();
//                    $girl_new->last_seen = $user['last_seen']['time'];
//                    if ($girl_new->url_photo !== $user['photo_200']) {
//                        try {
//                            Storage::disk('public')->put('friends/'.$girl_new->id.'_photo.jpg', file_get_contents($user['photo_200']));
//                            $girl_new->photo = 'storage/friends/'.$girl_new->id.'_photo.jpg';
//                            $girl_new->save();
//                        } catch (\Exception $e) {
//                            continue;
//                        }
//                    }
////                    $girl_new->url_photo = $user['photo_200'];
//                    if(isset($user['instagram'])) {
//                        $girl_new->instagram = 'https://instagram.com/'.$user['instagram'];
//                    }
//                    else
//                    {
//                        $girl_new->instagram = '---';
//                    }
//                    $girl_new->save();
//                    echo 'СОХРАНЕНИЕ'.$key;
//                }
//                ++$counter;
//            }
//            $offset += 1000;
//        }

        $application->worked = 0;
        $application->save();
    }
}
