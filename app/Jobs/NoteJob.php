<?php

namespace App\Jobs;

use App\Models\Girl;
use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use VK\Client\VKApiClient;

class NoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $title;
    public $girls_id;
    public $access_token;

    /**
     * Create a new job instance.
     *
     * @param $title
     * @param $girls
     * @param $access_token
     */
    public function __construct($title, $girls_id, $access_token)
    {
        $this->title = $title;
        $this->girls_id = $girls_id;
        $this->access_token = $access_token;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $note = Note::create($this->title);

        $vk = new VKApiClient();
        $girls = $vk->users()->get($this->access_token, array(
            'user_ids' => $this->girls_id,
            'fields' => 'photo_200,city,sex,bdate,last_seen'
        ));

        foreach ($girls as $girl) {
            $exist_girl = Girl::where('url', 'LIKE', '%'.$girl['id'])->first();
            if (!$exist_girl) {
                $new_girl = new Girl();
                $new_girl->url = 'https://vk.com/id'.$girl['id'];
                $new_girl->first_name = $girl['first_name'];
                $new_girl->last_name = $girl['last_name'];
                $new_girl->photo = $girl['photo_200'];
                if (isset($girl['last_seen'])) {
                    $new_girl->last_seen = $girl['last_seen']['time'];
                }
                else {
                    $new_girl->last_seen = '---';
                }
                if (isset($girl['bdate'])) {
                    $new_girl->bdate = $girl['bdate'];
                }
                else {
                    $new_girl->bdate = '---';
                }
                $new_girl->save();

                $new_girl->notes()->attach($note);
            }
            else {
                $exist_girl->notes()->attach($note);
            }
        }
    }
}
