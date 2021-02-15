<?php

namespace App\Http\Controllers;

use App\Models\Channel;
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
        $channels = auth()->user()->channels()
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
     * @param  \App\Models\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function show(Channel $channel)
    {
        $this->authorize('view', $channel);
        return view('channels.show', ['channel' => $channel]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Channel  $channels
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
     * @param  \App\Models\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function update(ChannelUpdateRequest $request, Channel $channel)
    {
        $this->authorize('update', $channel);

        $channel->name = $request->name;
        $channel->email = $request->email;
        $channel->mobile = $request->mobile;
        $channel->notes = $request->notes;
        $channel->save();

        return redirect()->route('channels.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Channel  $channels
     * @return \Illuminate\Http\Response
     */
    public function destroy(Channel $channel)
    {
        $channel->delete();
        return redirect()->route('channels.index');
    }

}
