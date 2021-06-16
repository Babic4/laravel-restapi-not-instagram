<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FollowController extends Controller
{
    public function check(Request $request){
        $follow = DB::table('following')
        ->where('userNickname', $request->nickname)
        ->where('followingNickname', $request->profile)
        ->first();
        if(is_null($follow)){
            return response()->json(['error' => true, 'message' => 'Not found'], 404);
        }
        return response()->json($follow,200);
    }

    public function checkbox(Request $request){
        $follow = DB::table('following')
        ->where('userNickname', $request->userNickname)
        ->where('followingNickname', $request->followingNickname)
        ->first();
        if(is_null($follow)){
            $follow = Follow::create($request->all());
            return response()->json($follow, 201);
        }
        $follow = Follow::find($follow->id);
        $follow->delete();
        return response()->json('', 204);
    }

    public function countF(Request $request){
        $follow = Follow::where('followingNickname', $request->nickname)->count();
        return response()->json($follow,200);
    }

    public function countS(Request $request){
        $follow = Follow::where('userNickname', $request->nickname)->count();
        return response()->json($follow,200);
    }

    public function showF(Request $request){
        $follows = DB::table('users')
        ->join('following', 'users.nickname', '=', 'following.userNickname')
        ->where('following.followingNickname', $request->nickname)
        ->get('users.*');
        foreach ($follows as &$follow) {
            $follow->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($follow->avatar));
        }
        return response()->json($follows,200);
    }

    public function showS(Request $request){
        $follows = DB::table('users')
        ->join('following', 'users.nickname', '=', 'following.followingNickname')
        ->where('following.userNickname', $request->nickname)
        ->get('users.*');
        foreach ($follows as &$follow) {
            $follow->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($follow->avatar));
        }
        return response()->json($follows,200);
    }

    // public function store(Request $request){
    //     $follow = DB::table('following')
    //     ->where('userNickname', $request->userNickname)
    //     ->where('followingNickname', $request->followingNickname)
    //     ->first();
    //     if(is_null($follow)){
    //         $follow = Follow::create($request->all());
    //     } else {
    //         return response()->json(['error' => true, 'message' => 'Prohibited'], 403);
    //     }
    //     return response()->json($follow, 201);
    // }

    // public function destroy(Request $request, $id){
    //     $follow = Follow::find($id);
    //     if(is_null($follow)){
    //         return response()->json(['error' => true, 'message' => 'Not found'],404);
    //     }
    //     $follow->delete();
    //     return response()->json('', 204);
    // }
}
