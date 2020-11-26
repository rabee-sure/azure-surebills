<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Http\Requests\ChannelRequest;
use App\Http\Requests\ChannelUpdateRequest;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $channels = Channel::where('user_id', auth()->user()->id)
            ->orderBy('id', 'desc')
            ->paginate($request->get('per_page', 10));
        return view('channels.index',  ['channels' => $channels]);
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
    public function store(ChannelRequest $request)
    {
        Channel::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'notes' => $request->notes,
            'user_id' => auth()->user()->id,
        ]);

        return redirect()->route('channels.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channel)
    {
        return view('channels.show', ['channel' => $channel]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function edit(Channel $channel)
    {
        return view('channels.edit', ['channel' => $channel]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function update(ChannelUpdateRequest $request, Channel $channels)
    {
        $channels->name = $request->name;
        $channels->email = $request->email;
        $channels->mobile = $request->mobile;
        $channels->notes = $request->notes;
        $channels->save();

        return redirect()->route('channels.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel)
    {
        $channel->delete();
        return redirect()->route('channels.index');
    }

}
