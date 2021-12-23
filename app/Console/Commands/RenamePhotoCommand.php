<?php

namespace App\Console\Commands;

use App\Models\Girl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RenamePhotoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'photo:rename';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Переименовать фотки';

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
        $path =  Storage::path('friends/1_photo.jpg');
        dd($path);
        Girl::chunk(1000, function ($girls) {
            $query = [];
            foreach ($girls as $girl) {
                $removeChar = ["https://", "http://", "/", 'vk.com', 'id'];
                $vk_id = str_replace($removeChar, "", $girl->url);
                $query[]=[
                    'id' => $girl->id,
                    'vk_id' => $vk_id,
                    'photo' => 'storage/'.$vk_id.'_photo.jpg'
                ];
                Storage::move('storage/public/'.$girl->id.'_photo.jpg', 'storage/public/'.$vk_id.'_photo.jpg');
                dd();
            }
            Girl::upsert($query, ['id'], ['vk_id', 'photo']);
            $this->info('ОП МИЗАНТРОП');
        });
    }
}
