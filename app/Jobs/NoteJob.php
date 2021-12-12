<?php

namespace App\Jobs;

use App\Events\GroupAddedEvent;
use App\Events\NoteAdded;
use App\Events\NoteAddedEзvent;
use App\Events\ProgressAddedForNoteEvent;
use App\Models\Girl;
use App\Models\Note;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
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
        $note = Note::firstOrCreate(
            ['title' => $this->title]
        );

        $vk = new VKApiClient();
        $girls = $vk->users()->get($this->access_token, array(
            'user_ids' => $this->girls_id,
            'fields' => 'photo_200,city,sex,bdate,last_seen,connections'
        ));

        $cicles = 100/count($girls);
        $progress = 0;
        $status = 'Найдено '.count($girls).' пользователей';

        $note->progress = $progress;
        $note->status = $status;
        $note->save();

        event(new ProgressAddedForNoteEvent($progress, $note->id, $status));

        $count = 1;
        foreach ($girls as $girl) {
            $new_girl = Girl::firstOrCreate(
                ['url' => 'https://vk.com/id'.$girl['id']],
                [
                    'first_name' => $girl['first_name'],
                    'last_name' => $girl['last_name'],
                    'last_seen' => (isset($girl['last_seen'])) ? $girl['last_seen']['time'] : '0',
                    'bdate' => (isset($girl['bdate'])) ? $girl['bdate'] : '---',
                    'photo' => '---',
                    'url_photo' => $girl['photo'],
                    'instagram' => (isset($girl['instagram'])) ? 'https://instagram.com/'.$girl['instagram'] : '---',
                ]
            );

            if (isset($girl['photo'])) {
                try {
                    Storage::disk('public')->put($new_girl->id.'_photo.jpg', file_get_contents($girl['photo']));
                    $new_girl->photo = 'storage/'.$new_girl->id.'_photo.jpg';
                    $new_girl->save();
                } catch (\Exception $e) {
                    continue;
                }
            }

            $new_girl->notes()->syncWithoutDetaching($note);

            $progress += $cicles;
            $status = 'Сохранение пользователей '.$count.'/'.count($girls);

            $note->progress = $progress;
            $note->status = $status;
            $note->save();

            event(new ProgressAddedForNoteEvent($progress, $note->id, $status));

            $count++;
        }
        $count = $note->loadCount('girls')->girls_count;
        $progress = 100;
        $status = 'Найдено '.$count.' пользователей';
        $note->progress = $progress;
        $note->status = $status;
        $note->save();
        event(new ProgressAddedForNoteEvent($progress, $note->id, $status));
    }
}
