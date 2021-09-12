<?php

namespace App\Http\Controllers;

use App\Models\Girl;
use App\Models\Group;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

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

    public function getGirlFromGroup($id)
    {
        $group = Group::with('girls')->find($id);
        $girls = $group->girls()->with('groups')->paginate(30);
        $this->setDateFromTimestamp($girls);
        return response()->json($girls);
    }

    public function getGirlNormal()
    {
//        $girls = Girl::where('need_to_write', 1)
//            ->orderBy('last_seen', 'desc')
//            ->with('groups')
//            ->paginate(30);
//        $this->setDateFromTimestamp($girls);
//        return response()->json($girls);
        $client = new Client();
        $response = $client->get('http://reddit.com/login', [
            'auth' => [
                'username',
                'password'
            ]
        ]);
        dd($response->getBody()->getContents());
        echo $res->getStatusCode();
// "200"
        echo $res->getHeader('content-type')[0];
// 'application/json; charset=utf8'
        echo $res->getBody();
        dd();
    }

    public function like(Request $request) {
        $id = $request->input('id');
        $girl = Girl::find($id);
        $girl->wrote = 0;
        $girl->need_to_write = 1;
        $girl->save();
    }

    public function dislike(Request $request) {
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
}
