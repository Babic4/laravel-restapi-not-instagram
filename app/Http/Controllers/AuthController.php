<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'nickname' => 'required|max:30',
            'password' => 'required',
            'avatar' => 'required',
            'phone' => 'required|max:17',
            'email' => 'required',
        ]);

        $image = $request->avatar; // your base64 encoded
        $image = strstr($image, ",");
        $image = str_replace(',', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time(). $request->nickname .'.png';

        Storage::disk('public')->put($imageName, base64_decode($image));

        $validatedData['avatar'] = $imageName;

        $validatedData['password'] = Hash::make($request->password);

        $user = User::create($validatedData);

        $accessToken = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $accessToken], 201);
    }

    public function signin(Request $request)
    {
        $loginData = $request->validate([
            'nickname' => 'required|max:30',
            'password' => 'required'
        ]);

        if (!auth()->attempt($loginData)) {
            if(is_null(User::find($loginData['nickname']))){
                return response(['message' => 'Пользователя с таким именем не существует!', 'code' => 1], 400);
            }
            else {
                if(!Hash::check($loginData['password'], User::find($loginData['nickname'])->password))
                    return response(['message' => 'Неверный пароль!', 'code' => 2], 400);
                else 
                    return response(['message' => 'I dont know'], 400);
            }
        }

        
        auth()->user()->avatar = "data:image/png;base64," . base64_encode(Storage::disk('public')->get(auth()->user()->avatar));

        $accessToken = auth()->user()->createToken('authToken')->accessToken;

        return response(['user' => auth()->user(), 'access_token' => $accessToken]);
    }
}
