<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $name = $request->query('name');
        // if(is_set($name)){

        // }

        // $messages = DB::table('messages')->get();
        // dd($messages);
        return response()->json(User::get(),200);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user=User::create($request->all());
        return response()->json($user, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($nickname)
    {
        $user = User::find($nickname);
        if(is_null($user)){
            return response()->json(['error' => true, 'message' => 'Not found'], 404);
        }
        $user->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($user->avatar));
        return response()->json($user,200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $nickname)
    {
        $user = User::find($nickname);
        if(is_null($user)){
            return response()->json(['error' => true, 'message' => 'Not found'],404);
        }
        $user->update($request->all());
        return response()->json($user, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $nickname)
    {
        $user = User::find($nickname);
        if(is_null($user)){
            return response()->json(['error' => true, 'message' => 'Not found'],404);
        }
        $user->delete();
        return response()->json('', 204);
    }

    public function email($email){
        $user = User::where('email', $email)->first();
        if(is_null($user)){
            return response()->json(['error' => true, 'message' => 'Not found'], 404);
        }
        return response()->json($user,200);
    }

    public function search(Request $request){
        $users = User::where('nickname', 'like', $request->keyword.'%')->get();
        foreach ($users as &$user) {
            $user->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($user->avatar));
        }
        return response()->json($users, 200);
    }
}
