<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function filter(Request $request){

        $image = $request->photo; // your base64 encoded
        $image = strstr($image, ",");
        $image = str_replace(',', '', $image);
        $image = str_replace(' ', '+', $image);
        $data = base64_decode($image);

        $im = imagecreatefromstring($data);


        if( $request->type == "brightness"){
            if($im && imagefilter($im, IMG_FILTER_BRIGHTNESS, $request->number))
            {
        
                ob_start(); // Let's start output buffering.
                imagepng($im); //This will normally output the image, but because of ob_start(), it won't.
                $contents = ob_get_contents(); //Instead, output above is saved to $contents
                ob_end_clean(); //End the output buffer.
            
                $dataUri = "data:image/png;base64," . base64_encode($contents);
            }
        } elseif ($request->type == "contrast") {
            if($im && imagefilter($im, IMG_FILTER_CONTRAST, $request->number))
            {
        
                ob_start(); // Let's start output buffering.
                imagepng($im); //This will normally output the image, but because of ob_start(), it won't.
                $contents = ob_get_contents(); //Instead, output above is saved to $contents
                ob_end_clean(); //End the output buffer.
            
                $dataUri = "data:image/png;base64," . base64_encode($contents);
            }
        }

        return response()->json($dataUri, 200);
    }

    public function allFilter(Request $request){

        $image = $request->photo; // your base64 encoded
        $image = strstr($image, ",");
        $image = str_replace(',', '', $image);
        $image = str_replace(' ', '+', $image);
        $data = base64_decode($image);
        $im = imagecreatefromstring($data);

        $arrayDataUri = array();

        if($im && imagefilter($im, IMG_FILTER_NEGATE))
        {

            ob_start(); // Let's start output buffering.
            imagepng($im); //This will normally output the image, but because of ob_start(), it won't.
            $contents = ob_get_contents(); //Instead, output above is saved to $contents
            ob_end_clean(); //End the output buffer.
        
            $dataUri = "data:image/png;base64," . base64_encode($contents);
            array_push($arrayDataUri, $dataUri);
        }

        $im = imagecreatefromstring($data);

        if($im && imagefilter($im, IMG_FILTER_GRAYSCALE))
        {

            ob_start(); // Let's start output buffering.
            imagepng($im); //This will normally output the image, but because of ob_start(), it won't.
            $contents = ob_get_contents(); //Instead, output above is saved to $contents
            ob_end_clean(); //End the output buffer.
        
            $dataUri = "data:image/png;base64," . base64_encode($contents);
            array_push($arrayDataUri, $dataUri);
        }

        $im = imagecreatefromstring($data);

        if($im && imagefilter($im, IMG_FILTER_MEAN_REMOVAL))
        {

            ob_start(); // Let's start output buffering.
            imagepng($im); //This will normally output the image, but because of ob_start(), it won't.
            $contents = ob_get_contents(); //Instead, output above is saved to $contents
            ob_end_clean(); //End the output buffer.
        
            $dataUri = "data:image/png;base64," . base64_encode($contents);
            array_push($arrayDataUri, $dataUri);
        }

        
        return response()->json($arrayDataUri, 200);
    }
}
