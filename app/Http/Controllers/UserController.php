<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\UserType;
use App\User;
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     **/
    public function index()
    {
        $columns = $this->_columnBuilder(['#','Full Names','Email Address','Account Type','Last Access', 'Allocation Notification', 'Allocation Notification Date', 'Action']);
        if(in_array(env('APP_LAB'), [5, 6])) $columns = $this->_columnBuilder(['#','Full Names','Email Address','Account Type','Last Access', 'Allocation Notification', 'Allocation Notification Date', 'Covid Allowed', 'Action']);
        if(env('APP_LAB') == 7) $columns = $this->_columnBuilder(['#','Full Names','MFL Code','Facility','Email Address','Account Type','Last Access', 'Action']);

        $row = "";

        $users = User::select('users.*','user_types.user_type')
            ->join('user_types', 'user_types.id', '=', 'users.user_type_id')
            ->when(true, function($query){
                return $query->where('users.user_type_id', '<>', 5);
                

                if(env('APP_LAB') != 7) return $query->where('users.user_type_id', '<>', 5);
                return $query->leftJoin('facilitys', 'facilitys.id', '=', 'users.facility_id')
                    ->addSelect('facilitys.name', 'facilitycode');
            })            
            ->where('users.email', '!=', 'rufus.nyaga@ken.aphl.org')
            ->get();

        foreach ($users as $key => $value) {
            $id = md5($value->id);
            $passreset = url("user/passwordReset/$id");
            $statusChange = url("user/status/$id");
            $delete = url("user/delete/$id");
            $alocationNotificationStatus = ($value->allocation_notification == 1) ? "<span class='badge badge-success'>YES</span>" : '';
            $allocationNotificationDate = (null !== $value->allocation_notification_date) ? date('l, d F Y', strtotime($value->allocation_notification_date)) : '';
            $allocationLinkText = ($value->allocation_notification == 0) ? 'Set' : 'Remove';
            $row .= '<tr>';
            $row .= '<td>'.($key+1).'</td>';
            $row .= '<td>'.$value->full_name.'</td>';
            if(env('APP_LAB') == 7){
                $row .= '<td>'.$value->facilitycode.'</td>';                
                $row .= '<td>'.$value->name.'</td>';                
            }
            $row .= '<td>'.$value->email.'</td>';
            $row .= '<td>'.$value->user_type.'</td>';
            $row .= '<td>'.date('l, d F Y', strtotime($value->last_access)).'</td>';
            if(env('APP_LAB') != 7){
            $row .= '<td>'. $alocationNotificationStatus .'</td>';
            $row .= '<td>'. $allocationNotificationDate .'</td>';
            }
            if(in_array(env('APP_LAB'), [5, 6])){
                if($value->covid_allowed) $row .= '<td>True</td>'; 
                else{
                    $row .= '<td> </td>';
                }
                               
            }
            $row .= '<td><a href="'.$passreset.'">Reset Password</a> | <a href="'.$statusChange.'">Delete</a> | <a href="'.url('user/'.$value->id).'">Edit</a> | <a href="'.url('allocationcontact/'.$value->id).'">' . $allocationLinkText .' Allocation Contact</a></td>';
            $row .= '</tr>';
        }

        return view('tables.display', compact('columns','row'))->with('pageTitle', 'Users');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        /*$accounts = UserType::whereNull('deleted_at')->when((env('APP_LAB') != 7), function($query){ 
            return $query->where('id', '<>', 5);
        })->get();*/
        $accounts = UserType::whereNull('deleted_at')->where('id', '<>', 5)->get();
        $partners = DB::table('partners')->get();
        $quarantine_sites = [];
        if(env('APP_LAB') != 8) $quarantine_sites = DB::table('quarantine_sites')->get();
        $labs = DB::table('labs')->get();

        return view('forms.users', compact('accounts', 'partners', 'quarantine_sites', 'labs'))->with('pageTitle', 'Add User');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', '=', $request->email)->count() > 0) {
            session(['toast_message'=>'User already exists', 'toast_error'=>1]);
            return redirect()->route('user.add');
        } else {
            $user = User::where($request->only(['email']))->onlyTrashed()->first();
            if($user) $user->deleted_at = null;
            else{
                $user = new User;
            }

            $user->fill($request->only(['user_type_id', 'lab_id', 'surname', 'oname', 'email', 'password', 'facility_id', 'telephone', 'covid_allowed']));
            $user->password = $request->input('password');

            if(!$user->lab_id) $user->lab_id = auth()->user()->lab_id;
            $user->save();
            session(['toast_message'=>'User created succesfully']);

            if ($request->submit_type == 'release')
                return redirect()->route('users');

            if ($request->submit_type == 'add')
                return redirect()->route('user.add');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user) {

        $accounts = UserType::whereNull('deleted_at')->where('id', '<>', 5)->get();
        $partners = DB::table('partners')->get();
        $quarantine_sites = [];
        if(env('APP_LAB') != 8) $quarantine_sites = DB::table('quarantine_sites')->get();
        $labs = DB::table('labs')->get();

        return view('forms.users', compact('accounts', 'user', 'partners', 'quarantine_sites', 'labs'))->with('pageTitle', 'Add User');
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
        if(!$request->input('password')) { // No password for edit

            $userData = $request->only(['user_type_id','email','surname','oname','telephone', 'facility_id', 'covid_allowed']);
            
            $user = User::find($id);
            $user->fill($userData);
            if($request->input('lab_id')) $user->lab_id = $request->input('lab_id');
            $user->save();
        } else {
            $user = self::__unHashUser($id);

            if (!empty($user)) {
                $user->password = $request->password;
                $user->update();
                session(['toast_message'=>'User password succesfully updated']);
            } else {
                session(['toast_message'=>'User password succesfully updated','toast_error'=>1]);
            }
        }
                
        if (isset($request->user)) {
            return back();
        } else {
            return redirect()->route('users');
        }      
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


    public function delete($id) {
        $user = self::__unHashUser($id);
        if(!$user->facility_id) $user->delete();
        else{
            session(['toast_error' => 1, 'toast_message' => 'You cannot delete this user. Update the password to lock the account.']);
        }

        return back();
    }

    public function activity(User $user, $year = null, $month = null) {
        if ($year==null || $year=='null'){
            if (session('activityYear')==null)
                session(['activityYear' => Date('Y')]);
        } else {
            session(['activityYear'=>$year]);
        }

        if ($month==null || $month=='null'){
            session()->forget('activityMonth');
        } else {
            session(['activityMonth'=>(strlen($month)==1) ? '0'.$month : $month]);
        }

        $year = session('activityYear');
        $month = session('activityMonth');
        $monthName = "";
        
        if (null !== $month) 
            $monthName = "- ".date("F", mktime(null, null, null, $month));

        $data = (object)['year'=>$year,'monthName'=>$monthName, 'month'=>$month];
        
        if (!empty($user->toArray())) {
            return view('users.user-activity', compact('user'))->with('pageTitle', 'User Activity');
        } else {
        $users = User::whereNotIn('user_type_id', [2,5,6])->get();
        return view('tables.users-activity', compact('users'), compact('data'))->with('pageTitle', 'Users Activity');
        }
    }

    public function switch_user($id)
    {
        $this->auth_user(0);
        $user = User::findOrFail($id);
        Auth::logout();
        Auth::login($user);
        return back();
    }

    public function passwordreset($id = null)
    {
        $user = null;
        if (null == $id) {
            $user = 'personal';
            return view('forms.passwordReset', compact('user'))->with('pageTitle', 'Password Reset');
        } else {
            $user = self::__unHashUser($id);
            return view('forms.passwordReset', compact('user'))->with('pageTitle', 'Password Reset');
        }
    }

    public function edit_password(Request $request, $id)
    {
        $user = self::__unHashUser($id);

        if (!empty($user)) {
            $user->password = $request->password;
            $user->update();
            session(['toast_message'=>'User password succesfully updated']);
        } else {
            session(['toast_message'=>'User password succesfully updated','toast_error'=>1]);
        }  
        
        if (isset($request->user)) {
            return back();
        } else {
            return redirect()->route('users');
        }          
    }

    public function allocationcontact(User $user)
    {
        $text = '';
        if ($user->allocation_notification == 0){
            $user->allocation_notification = 1;
        } else {
            $user->allocation_notification = 0;
            $text = 'not';
        }
        $user->save();
        session(['toast_message' => $user->full_name . ' will ' . $text . ' be receiving allocation notifications']);
        return back();
    }

    private static function __unHashUser($hashed){
        $user = [];
        foreach (User::get() as $key => $value) {
            if ($hashed == md5($value->id)) {
                $user = $value;
                break;
            }
        }

        return $user;
    }
}
