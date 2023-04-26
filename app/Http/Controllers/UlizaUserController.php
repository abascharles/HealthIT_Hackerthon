<?php

namespace App\Http\Controllers;

use App\User;
use DB;
use Str;
use Hash;

use App\Notifications\NewUlizaUserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UlizaUserController extends Controller
{

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return redirect('uliza-form');
        }else{
            session(['toast_error' => 1, 'toast_message' => 'These credentials do not match our records.']);
            return back();            
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect('uliza/uliza');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        if($user->uliza_reviewer) abort(403);
        $users = User::with(['twg', 'user_type'])
            ->when(true, function($query) use($user){
                if($user->uliza_secretariat) return $query->where(['twg_id' => $user->twg_id, 'user_type_id' => 104]);
                else{
                    return $query->where('user_type_id', '>', 100);
                }
            })
            ->withTrashed()
            ->get();
        return view('uliza.tables.users', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user_types = DB::table('user_types')->where('id', '>', 101)->get();
        $twgs = DB::table('uliza_twgs')->get();        
        return view('uliza.forms.user', compact('twgs', 'user_types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::where($request->only('email'))->withTrashed()->first();
        if($user){
            session(['toast_error' => 1, 'toast_message' => 'The user already exists.']);
            return back();
        }

        $user = new User;
        $user->fill($request->all());
        // $user->password = 'password';
        $password = \Str::random(15);
        $user->password = $password;
        $user->save();
        session(['toast_message' => 'The user has been created']);
        try {
            $user->notify(new NewUlizaUserNotification($password));
        } catch (\Exception $e) {
            session(['toast_error' => 1, 'toast_message' => 'The user has been created but the email could not go out.']);
        }

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {     
        $uliza_user = User::findOrFail($id);
        $user_types = DB::table('user_types')->where('id', '>', 101)->get();
        $twgs = DB::table('uliza_twgs')->get();   
        return view('uliza.forms.user', compact('twgs', 'user_types', 'uliza_user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {    
        $user = User::findOrFail($id);
        $u = User::where($request->only('email'))->where('id', '!=', $user->id)->first();
        if($u){
            session(['toast_error' => 1, 'toast_message' => 'The user already exists.']);
            return back();
        }
        $user->fill($request->all());
        // $user->password = 'password';
        $user->save();
        session(['toast_message' => 'The user has been updated']);
        return redirect('uliza-user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        session(['toast_message' => 'The user has been deactivated.']);
        return back();
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        session(['toast_message' => 'The user has been restored.']);
        return back();
    }

    public function resend_email($id)
    {
        $user = User::findOrFail($id);
        $password = \Str::random(15);
        $user->password = $password;
        $user->save();
        session(['toast_message' => 'The email has been sent.']);
        try {
            $user->notify(new NewUlizaUserNotification($password));
        } catch (\Exception $e) {
            session(['toast_error' => 1, 'toast_message' => 'The user has been created but the email could not go out.']);
        }

        return back();

    }

    public function change_password()
    {
        return view('uliza.uliza-update-password');
    }

    public function update_password(Request $request)
    {
        $user = auth()->user();
        $user->password = $request->input('password');
        $user->save();
        session(['toast_message' => 'The password has been updated']);
        return redirect('uliza-form');
    }
}
