<?php

namespace App\Http\Controllers;

use App\Events\GroupAddedEvent;
use App\Events\NoteAdded;
use App\Events\ProgressAddedEvent;
use App\Models\Book;
use App\Models\Girl;
use App\Models\Group;
use App\Models\Note;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->orderBy(DB::raw('groups_count + notes_count'), 'DESC')
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
        $note = Note::first();
        event(new NoteAdded($note));
        dd('ky');
        $girls = Girl::where('url', 'LIKE', 'http:%')->get();
        foreach ($girls as &$girl) {
            $girl->url = str_replace('http', 'https', $girl->url);
            $girl->save();
        }
        dd($girls);
    }
}
