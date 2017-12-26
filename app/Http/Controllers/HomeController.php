<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function uploadImage(Request $request){
        $req = $request->all();
        // return $req;
        $respond = '';
        if($req['image']){
            $image = \Image::make($request->image)->resize(945,325);
            $filename = md5($request->image.time()).'.jpg';
            $fileUrl = 'images/tmp/'.$filename;
            \Storage::disk('public')->put($fileUrl, $image->stream());
            $fileUrlThumb = 'content/header-thumb/'.$filename;
            $image = $image->crop(300,300);
            \Storage::disk('public')->put($fileUrlThumb, $image->stream());
            
            return [
                'respond' => 'true',
                'data' => asset($fileUrl)
            ];
        }
        return [
                'respond' => 'false',
                'message' => 'Error Upload Image'
            ];
    }
}
