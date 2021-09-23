<?php

namespace App\Http\Controllers;

use App\Events\GroupAddedEvent;
use App\Events\NoteAdded;
use App\Events\ProgressAddedEvent;
use App\Models\Application;
use App\Models\Book;
use App\Models\Girl;
use App\Models\Group;
use App\Models\Note;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use VK\Client\VKApiClient;

class GirlController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getGirlFromGroup($id)
    {
        $group = Group::with('girls')->find($id);
        $girls = $group
            ->girls()
            ->with('groups', 'notes')
            ->orderBy('last_seen', 'DESC')
            ->paginate(30);
        $this->setDateFromTimestamp($girls);
        return response()->json($girls);
    }

    public function getGirlNormal()
    {
        $girls = Girl::where('need_to_write', 1)
            ->with('groups', 'notes')
            ->withCount('notes', 'groups')
            ->orderBy('last_seen', 'DESC')
            ->orderBy(DB::raw('groups_count + notes_count'), 'DESC')
            ->paginate(30);
        $this->setDateFromTimestamp($girls);
        return response()->json($girls);
    }

    public function getGirlFromNote($id)
    {
        $note = Note::with('girls')->find($id);
        $girls = $note->girls()
            ->with('notes', 'groups')
            ->withCount('notes', 'groups')
            ->orderBy('last_seen', 'DESC')
            ->paginate(30);
        $this->setDateFromTimestamp($girls);
        return response()->json($girls);
    }

    public function like(Request $request)
    {
        $id = $request->input('id');
        $girl = Girl::find($id);
        $girl->wrote = 0;
        $girl->need_to_write = 1;
        $girl->save();
    }

    public function dislike(Request $request)
    {
        $id = $request->input('id');
        $girl = Girl::find($id);
        $girl->wrote = 1;
        $girl->need_to_write = 0;
        $girl->save();
    }

    private function setDateFromTimestamp($girls)
    {
        foreach ($girls as &$girl) {
            $girl->last_seen = Carbon::createFromTimestamp($girl->last_seen)
                ->format('H:i d/m/Y');
        }
        return $girls;
    }

    public function searchGirls(Request $request)
    {
        $search_string = $request->input('search_string');
        $array = explode(' ', $search_string);
        $girls = Girl::where('url', $search_string)
            ->orWhere('first_name', 'LIKE', '%'.$search_string.'%')
            ->orWhere('last_name', 'LIKE', '%'.$search_string.'%');
        if (count($array) === 2) {
            $girls = $girls
                ->orWhere(function ($query) use ($array) {
                    $query->orWhere('first_name', $array[0])
                        ->where('last_name',$array[1]);
            })
                ->orWhere(function ($query) use ($array) {
                    $query->orWhere('first_name', $array[1])
                        ->where('last_name', $array[0]);
                });
        }
        $girls = $girls->with('posts', 'groups')
            ->withCount('groups')
            ->orderBy('groups_count', 'DESC')
            ->get();
        return response()->json($girls);
    }

    public function fix()
    {
        $application = Application::first();
        $access_token = $application->access_token;

        $groups = Group::all();
        $vk = new VKApiClient();

        $url = 'https://vk.com/zzzx10';
        $remove_char = ["https://vk.com/club", "https://vk.com/public", "https://vk.com/"];
        $group_id = str_replace($remove_char, "", $url);

        $response = $vk->groups()->getById($access_token, array(
            'group_ids' => $group_id,
        ));
        dd($response);

        foreach ($groups as $group) {
            $remove_char = ["https://vk.com/club", "https://vk.com/public", "https://vk.com/"];
            $group_id = str_replace($remove_char, "", $group->url_group);

            $response = $vk->groups()->getById($access_token, array(
                'group_ids' => $group_id,
            ));

            $group->url_group = 'https://vk.com/public'.$response[0]['id'];
            $group->title = $response[0]['name'];
            $group->image = $response[0]['photo_200'];
            $group->save();
            usleep(340000);
        }
        dd();


//        $groups = Group::all();
//        foreach ($groups as $group) {
//            $group->status = 'Найдено '.$group->loadCount('girls')->girls_count.' пользователей';
//            $group->progress = 100;
//            $group->save();
//        }
//        dd();

//        $notes = Note::all();
//        foreach ($notes as $note) {
//            $note->status = 'Найдено '.$note->loadCount('girls')->girls_count.' пользователей';
//            $note->progress = 100;
//            $note->save();
//        }
//        dd();
//        config(['database.connections.mysql.database' => 'priv']);

//        $girls = DB::table('girls')
//            ->join('girl_group', 'girls.id', '=', 'girl_group.girl_id')
//            ->join('groups', 'girl_group.group_id', '=', 'groups.id')
//            ->select('girls.*', 'groups.id as id_group', 'groups.title')
//            ->get();
//
//        dd($girls);

        $girls = Girl::all();


        $chickens = DB::table('chickens')
            ->join('chicken_note', 'chickens.id', '=', 'chicken_note.chicken_id')
            ->join('notes', 'chicken_note.note_id', '=', 'notes.id')
            ->select('chickens.*', 'notes.id as id_note', 'notes.title')
            ->get();
        $count = 1;
        foreach ($chickens as $chicken) {
            $girls = Girl::all();
            $filter = $girls->filter(function ($girl) use ($chicken) {
                return $girl->url === $chicken->url;
            });
            if ($filter->isEmpty()) {
                $girl = new Girl();
                $girl->url = $chicken->url;
                $girl->first_name = $chicken->first_name;
                $girl->last_name = $chicken->last_name;
                $girl->bdate = $chicken->bdate;
                $girl->photo = $chicken->photo;
                $girl->wrote = $chicken->is_pisal;
                $girl->need_to_write = $chicken->write;
                $girl->last_seen = $chicken->last_seen;
                $girl->age = $chicken->age;
                $girl->save();

                $note = Note::find($chicken->id_note);
                $girl->notes()->syncWithoutDetaching($note);
            }
            else {
                $girl = Girl::where('url', $chicken->url)->first();
                $note = Note::find($chicken->id_note);
                $girl->notes()->syncWithoutDetaching($note);
            }

            echo $count;
            ++$count;
        }
        dd();

        foreach ($girls as $girl) {
            foreach ($chickens as $chicken) {
                if ($chicken->url === $girl->url) {
                    $note = Note::find($chicken->id_note);
                    $girl->notes()->syncWithoutDetaching($note);
                }
                else {
                    $girl = new Girl();
                    $girl->url = $chicken->url;
                    $girl->first_name = $chicken->first_name;
                    $girl->last_name = $chicken->last_name;
                    $girl->bdate = $chicken->bdate;
                    $girl->photo = $chicken->photo;
                    $girl->wrote = $chicken->is_pisal;
                    $girl->need_to_write = $chicken->write;
                    $girl->last_seen = $chicken->last_seen;
                    $girl->age = $chicken->age;
                    $girl->save();

                    $note = Note::find($chicken->id_note);
                    $girl->notes()->syncWithoutDetaching($note);
                }

            }
        }

        dd('ok)');
        $girls = Girl::where('url', 'LIKE', 'http:%')->get();
        dd($girls);
        foreach ($girls as $girl) {
            $girl->url = str_replace('http', 'https', $girl->url);
            $girl->save();
            dd($girl);
        }
        dd($girls);
    }
}
