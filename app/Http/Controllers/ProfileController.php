<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use File;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 1){
            $user['id'] = Auth::user()->id;
            $user['username'] = Auth::user()->username;
            $user['firstname'] = Auth::user()->firstname;
            $user['lastname'] = Auth::user()->lastname;
            $user['email'] = Auth::user()->email;
            $user['phone'] = Auth::user()->phone;
            $user['avatar'] = "";
            $user['password'] = "";
            $user['password_confirmation'] = "";
        
            return view('profile.profile_index', compact('user'));
        }
        return abort('403', __('You are not authorized'));
    }


    public function get_client_profile()
    {
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 3){

            $client = Client::findOrFail($user_auth->id);

            $user['id'] = $client->id;
            $user['firstname'] = $client->firstname;
            $user['lastname'] = $client->lastname;
            $user['username'] = $client->username;
            $user['email'] = $client->email;
            $user['phone'] = $client->phone;
            $user['country'] = $client->country;
            $user['city'] = $client->city;
            $user['address'] = $client->address;
            $user['avatar'] = "";
            $user['password'] = "";
        
            return view('client.client_profile', compact('user'));
        }
        return abort('403', __('You are not authorized'));
    }




    public function Update_client_profile(Request $request , $id)
    {
        
        $user_auth = auth()->user();
        if ($user_auth->role_users_id == 3){

            $this->validate($request, [
                'firstname' => 'required|string|max:255',
                'lastname'  => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'email'     => Rule::unique('users')->ignore($id),
                'phone'     => 'nullable|numeric',
                'country'   => 'nullable|string|max:255',
                'city'      => 'nullable|string|max:255',
                'address'   => 'nullable|string|max:255',
                'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
                'password'  =>  'sometimes|nullable|string|min:6,'.$id,

            ], [
                'email.unique' => 'This Email already taken.',
            ]);

            $user = User::findOrFail($id);
            $current = $user->password;

            if ($request->password != null) {
                if ($request->password != $current) {
                    $pass = Hash::make($request->password);
                } else {
                    $pass = $user->password;
                }

            } else {
                $pass = $user->password;
            }

            $currentAvatar = $user->avatar;
            if ($request->avatar != null) {
                if ($request->avatar != $currentAvatar) {

                    $image = $request->file('avatar');
                    $filename = time().'.'.$image->extension();  
                    $image->move(public_path('/assets/images/avatar'), $filename);
                    $path = public_path() . '/assets/images/avatar';
                    $userPhoto = $path . '/' . $currentAvatar;
                    if (file_exists($userPhoto)) {
                        if ($user->avatar != 'no_avatar.png') {
                            @unlink($userPhoto);
                        }
                    }
                } else {
                    $filename = $currentAvatar;
                }
            }else{
                $filename = $currentAvatar;
            }


            $data = [];
            $data['firstname'] = $request['firstname'];
            $data['lastname'] = $request['lastname'];
            $data['username'] = $request['firstname'] .' '.$request['lastname'];
            $data['country'] = $request['country'];
            $data['city'] = $request['city'];
            $data['address'] = $request['address'];
            $data['email'] = $request['email'];
            $data['phone'] = $request['phone'];
            
            $user_data = [];
            $user_data['username'] = $request['firstname'] .' '.$request['lastname'];
            $user_data['email'] = $request->email;
            $user_data['password'] = $pass;
            $user_data['avatar'] = $filename;

            \DB::transaction(function () use ($request , $id , $user_data , $data) {

                User::whereId($id)->update($user_data);
                Client::find($id)->update($data);
                
            }, 10);
         
            return response()->json(['success' => true]);
        }

        return abort('403', __('You are not authorized'));
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


    public function updateProfile(Request $request , $id)
    {
        $this->validate($request, [
            'email' => 'required|string|email|max:255|unique:users',
            'email' => Rule::unique('users')->ignore($id),
            'username'  => 'required|string|max:255',
            'avatar'    => 'nullable|image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            'password'  =>  'sometimes|nullable|string|confirmed|min:6,'.$id,
        ], [
            'email.unique' => 'This Email already taken.',
        ]);

        $user = User::findOrFail($id);
        $current = $user->password;

        if ($request->password != null) {
            if ($request->password != $current) {
                $pass = Hash::make($request->password);
            } else {
                $pass = $user->password;
            }

        } else {
            $pass = $user->password;
        }

        $currentAvatar = $user->avatar;
        if ($request->avatar != null) {
            if ($request->avatar != $currentAvatar) {

                $image = $request->file('avatar');
                $filename = time().'.'.$image->extension();  
                $image->move(public_path('/assets/images/avatar'), $filename);
                $path = public_path() . '/assets/images/avatar';
                $userPhoto = $path . '/' . $currentAvatar;
                if (file_exists($userPhoto)) {
                    if ($user->avatar != 'no_avatar.png') {
                        @unlink($userPhoto);
                    }
                }
            } else {
                $filename = $currentAvatar;
            }
        }else{
            $filename = $currentAvatar;
        }


        User::whereId($id)->update([
            'username'  => $request['username'],
            'email'     => $request['email'],
            'avatar'    => $filename,
            'password'  => $pass,
        ]);

        return response()->json(['success' => true]);
    }
}
