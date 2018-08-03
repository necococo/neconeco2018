<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Storage;
use Validator;
use Image;
use App\Micropost;
use App\User;

class MicropostsController extends Controller
{
    public function index()
    {
        $data = [];
        if (\Auth::check()) {
            $user = \Auth::user();
            $microposts = DB::table('microposts')->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'microposts' => $microposts,
            ];
            $data += $this->counts($user);
            return view('microposts.index', $data);
        }else {
            return view('welcome');
        }
    }
   

    public function show($id)
    {
        $micropost = Micropost::find($id);
        return view('microposts.show', ['micropost' => $micropost]);
    }
    
    public function create()
    {
        $data = [];
        //user()はobjectらしい
        $user = \Auth::user();
        $microposts =$user->microposts();
        $data = ['user' => $user, 'microposts' => $microposts];
        $data += $this->counts($user);
        
        return view('microposts.create',$data);
    }
    
    
    
    public function store(Request $request)
    {
        $input = $request->all();
 
        $fileName = $input['filename']->getClientOriginalName();
        $fileName = time()."@".$fileName;
        
        $image = Image::make($input['filename']->getRealPath());
        //画像リサイズ
        $image->resize(null, 300, function ($constraint) {
        $constraint->aspectRatio();
        });
        $image->save(storage_path() . '/app/public/images/' .  $fileName);
        
        $micropost = new Micropost;
        $micropost->user_id = $request->user()->id;
        $micropost->image_path = 'storage/images/' .  $fileName;
        $micropost->save();
        
        return back()->with('message','ファイルはアップロードされました。');
    }
    

    public function destroy($id)
    { 
        $micropost = Micropost::find($id);

        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }
        return redirect('/');
    }  
}
