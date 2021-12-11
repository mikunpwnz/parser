<?php

namespace App\Console\Commands;

use App\Models\Application;
use App\Models\Group;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use VK\Client\VKApiClient;

class UpdateGroupsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'group:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление информации о группах';

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
        $application = Application::where('worked', 0)->first();
        $access_token = $application->access_token;

        $groups = Group::all();
        $group_ids = [];
        $count = Group::count();

        foreach ($groups as $group) {
            $remove_char = ["https://", "http://", "/", 'vk.com', '/public', '/club'];
            $group_ids [] = str_replace($remove_char, "", $group->url_group);
        }

        $vk = new VKApiClient();
        $response = $vk->groups()->getById($access_token, array(
            'group_ids' => $group_ids,
        ));

        foreach ($response as $key=>$item) {
            $group = Group::where('title', $item['name'])->first();
            $group->url_group = 'https://vk.com/public'.$item['id'];

            try {
                Storage::disk('public')->put('groups/'.$group->id.'_photo.jpg', file_get_contents($item['photo_200']));
                $group->image = 'storage/groups/'.$group->id.'_photo.jpg';
                $group->save();
            } catch (\Exception $e) {
                return;
            }

            $group->save();
            $this->info('Обработка'.$key.' из '.$count);
        }

    }
}
