<?php

namespace App\Http\Controllers;

use App\Events\ProgressAddedForNoteEvent;
use App\Jobs\NoteJob;
use App\Models\Application;
use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notes = Note::all();
        return response()->json($notes);
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $id = $request->input('girls_id');

        $girls_id_with_https = str_replace("\n", ',', $id);
        $girls_id_without_https = str_replace('https://vk.com/id', '', $girls_id_with_https);
        $girls_id = explode(',', $girls_id_without_https);

        $title = $request->input('title');
        $application = Application::where('worked', 0)->first();

        $job = (new NoteJob($title, $girls_id, $application->access_token));
        $this->dispatch($job);
        $response = ['message' => 'Музыка успешно добавлена'];
        return response()->json($response, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function socket()
    {
        $note = Note::find(1);
        $note->progress += 10;
        $note->save();
        event(new ProgressAddedForNoteEvent($note->progress, 1, 'y'));
    }
}
