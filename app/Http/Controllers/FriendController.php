<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $friends = Friend::where('need_to_write', 0)
            ->where('wrote', 0)
            ->orderBy('last_seen', 'DESC')->paginate(30);
        $this->setDateFromTimestamp($friends);
        return response()->json($friends);
    }

    public function indexNorm()
    {
        $friends = Friend::where('need_to_write', 1)->orderBy('last_seen', 'DESC')->paginate(30);
        $this->setDateFromTimestamp($friends);
        return response()->json($friends);
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
        //
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

    public function like(Request $request)
    {
        $id = $request->input('id');
        $friend = Friend::find($id);
        $friend->wrote = 0;
        $friend->need_to_write = 1;
        $friend->save();
    }

    public function dislike(Request $request)
    {
        $id = $request->input('id');
        $friend = Friend::find($id);
        $friend->wrote = 1;
        $friend->need_to_write = 0;
        $friend->save();
    }

    public function description(Request $request)
    {
        $id = $request->input('id');
        $description = $request->input('description');
        $friend = Friend::find($id);
        $friend->description = $description;
        $friend->save();
        return response()->json('Успешно сохранено!');
    }

    private function setDateFromTimestamp($friends)
    {
        foreach ($friends as &$friend) {
            $friend->last_seen = Carbon::createFromTimestamp($friend->last_seen)
                ->format('H:i d/m/Y');
        }
        return $friends;
    }
}
