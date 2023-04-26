<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SpiceworksController extends Controller
{
    public function create()
    {
        return view('spiceworks.create');
    }
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'email' => 'required|email',
        'subject' => 'required',
        'message' => 'required',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $data = [
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'subject' => $request->input('subject'),
        'message' => $request->input('message'),
    ];

    Mail::to('help@chukafreshdevs.on.spiceworks.com')->send(new SpiceworksTicket($data));

    return redirect()->back()->with('success', 'Ticket submitted successfully.');
}

}
