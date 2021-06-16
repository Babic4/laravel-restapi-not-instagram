<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class GalleryController extends Controller
{
    public function indexMy(Request $request){
        $gallery = Gallery::where('ownerPhoto', $request->nickname)->get();
        foreach ($gallery as &$image) {
            $image->photo = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($image->photo));
        }
        return response()->json($gallery,200);

    }

    public function indexOthers(Request $request){
        $gallery = Gallery::where('ownerPhoto', "!=", $request->nickname)->get();
        foreach ($gallery as &$image) {
            $image->photo = "data:image/png;base64," . base64_encode(Storage::disk('public')->get($image->photo));
        }
        return response()->json($gallery,200);

    }

    public function store(Request $request){

        $date = date('Y-m-d H:i:s');
        $request['datetimeAdd'] = $date;

        $image = $request->photo; // your base64 encoded
        $image = strstr($image, ",");
        $image = str_replace(',', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = time(). $request->ownerPhoto .'.png';

        Storage::disk('public')->put($imageName, base64_decode($image));

        $request['photo'] = $imageName;

        $image = Gallery::create($request->all());
        return response()->json($image, 201);
    }
}
