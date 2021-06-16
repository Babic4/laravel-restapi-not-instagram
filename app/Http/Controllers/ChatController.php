<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Events\SendMessage;

class ChatController extends Controller
{
    public function getConversations($nickname){

        $conversations  = array();

        $conversationsA = DB::table('conversations')
        ->join('messages', 'conversations.lastMessageId', '=', 'messages.id')
        ->join('users', 'conversations.receiver', '=', 'users.nickname')
        ->where('conversations.sender', $nickname)
        ->get();

        $conversationsB = DB::table('conversations')
        ->join('messages', 'conversations.lastMessageId', '=', 'messages.id')
        ->join('users', 'conversations.sender', '=', 'users.nickname')
        ->where('conversations.receiver', $nickname)
        ->get();

        foreach ($conversationsA as $item) {
            array_push($conversations, $item);
        }
        foreach ($conversationsB as $item) {
            array_push($conversations, $item);
        }

        foreach ($conversations as &$item) {
            $item->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($item->avatar));
        }

        return response()->json($conversations, 200);
    }

    public function getMessages($convId){
        $messages  = DB::table('messages')
        // ->join('users', 'messages.ownerMessage', '=', 'users.nickname')
        ->where('messages.convId', $convId)
        ->orderBy('messages.datetimeAdd')
        ->get();

        // foreach ($messages as &$item) {
        //     $item->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($item->avatar));
        // }
        
        return response()->json($messages, 200);
    }

    public function addMessage(Request $request){
        $date = date('Y-m-d H:i:s');
        $request['datetimeAdd'] = $date;
        $message = Message::create($request->all());

        // brodcast(new SendMessage ($message))->toOthers();

        return response()->json($message, 201);
    }

    public function reviewMessages(Request $request, $convId){
        $messages = Message::where('convId', $convId)
        ->where('ownerMessage', $request->user)
        ->where('status', 1)
        ->update(['status' => 0]);

        return response()->json($messages, 200);
    }

    public function isConversation(Request $request){
        $oneU = $request['nickname'];
        $twoU = $request['user'];
        $conversation = DB::table('conversations')
        ->join('messages', 'conversations.lastMessageId', '=', 'messages.id')
        ->join('users', 'messages.ownerMessage', '=', 'users.nickname')
        ->where(function($query) use ($oneU, $twoU) {
            return $query->where('conversations.receiver','=', $oneU)
                  ->where('conversations.sender','=', $twoU);
        })
        ->orWhere(function($query) use ( $oneU, $twoU) {
            return $query->where('conversations.receiver','=', $twoU)
                  ->where('conversations.sender','=',  $oneU);
        })
        ->first();
        if(is_null($conversation)){
            $user = User::find($request['user']);
            // $user->ownerMessage = $user->nickname;
            $user->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($user->avatar));
            return response()->json(['error' => true, 'message' => 'Not found', 'user'=> $user], 404);
        }
        return response()->json($conversation, 200);
    }

    public function addConv(Request $request){
        $conversation = Conversation::create($request->all());
        return response()->json($conversation, 201);
    }

    public function addLastMessage(Request $request, $convId){
        $conversation = Conversation::find($convId);                                
        $conversation->update(['lastMessageId' => $request->lastMessageId]);

        $message = Message::find($request->lastMessageId);

        event(new SendMessage($message));

        return response()->json($conversation, 200);
    }

    public function countM(Request $request){
        $nickname = $request['nickname'];
        $count = DB::table('conversations')
        ->join('messages', 'conversations.lastMessageId', '=', 'messages.id')
        ->where(function($query) use ($nickname) {
            return $query->where('conversations.sender', $nickname)
            ->where('messages.ownerMessage', '!=', $nickname)
            ->where('messages.status', 1);
        })
        ->orWhere(function($query) use ( $nickname) {
            return $query->where('conversations.receiver', $nickname)
            ->where('messages.ownerMessage', '!=', $nickname)
            ->where('messages.status', 1);
        })
        ->count();

        return response()->json($count, 200);
    }
}
