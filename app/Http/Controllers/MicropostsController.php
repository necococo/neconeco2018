<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Storage;
use Validator;
use Image;
use App\Micropost;
use App\User;
use App\Comment;

class MicropostsController extends Controller
{
    public function index()
    {
        
        if (\Auth::check()) {
            $microposts = DB::table('microposts')->orderBy('created_at', 'desc')->paginate(8);
            return view('microposts.index', ['microposts' => $microposts]);
        }else {
            return view('welcome');
        }
    }
   

    public function show($id)
    {
        $user = \Auth::user();
        $micropost = Micropost::find($id);
        $comments = $micropost->comments()->orderBy('created_at', 'desc')->get();
        $data = ['user' => $user, 'micropost' => $micropost, 'comments'=>$comments];
        
        return view('microposts.show',$data);
    }
    
      //編集    
    public function edit($id)
    {
        $data = [];
        $user = \Auth::user();
        $micropost = Micropost::find($id);
   
        // dd($micropost->map_long);
        $comments = $micropost->comments()->orderBy('created_at', 'desc')->get();
        $data = ['user' => $user, 'micropost' => $micropost, 'comments'=>$comments];
        return view('microposts.edit',$data);
        
    }
    
    public function update(Request $request, $id)
    {   
        $data = [];
        $validator = Validator::make($request->all(),[
        // 'photo' => 'required|image|max:5000',
        'search_tag' => 'nullable',
        'lat' => 'required',
        'long' => 'required',
        ]);
        
        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        
        $user = \Auth::user();
        $micropost = Micropost::find($id);
        $micropost->search_tag = $request->search_tag;
        $micropost->map_lat = $request->lat;
        $micropost->map_long = $request->long;
        $micropost->save();
        $comments = $micropost->comments()->orderBy('created_at', 'desc')->get();
        $data = ['user' => $user, 'micropost' => $micropost, 'comments'=>$comments];
        return view('microposts.show', $data)->with('updated','データは更新されました。');
    }
    
    public function create()
    {
        
        $data = [];
        $user = \Auth::user();
        $microposts =$user->microposts();
        $data = ['user' => $user, 'microposts' => $microposts];
        $data += $this->counts($user);
        
        return view('microposts.create',$data);
    }
    
    
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
        'photo' => 'required|image|max:5000',
        'search_tag' => 'nullable',
        'lat' => 'required',
        'long' => 'required',
        ]);
        
        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        
        $micropost = $request->user()->microposts()->create([
            'image_path' => $request->file('photo'),
            'search_tag' => $request->search_tag,
            'map_lat' => $request->lat,
            'map_long' => $request->long,
        ]);
        
        
        $path = Storage::disk('s3')->putFile('images', $request->file('photo'), 'public'); // s3/images/にアップ
        
        $url = Storage::disk('s3')->url($path);
        
        $micropost->image_path = $url;
        $micropost->save();
        return redirect()->route('microposts.show', ['id' => \Auth::id(), 'micropost' =>$micropost ])->with('success','ファイルはアップロードされました。');

    }
    

    public function destroy($id)
    { 
        $micropost = Micropost::find($id);
        if (\Auth::id() === $micropost->user_id) {
            $micropost->delete();
        }
        return redirect('/');
    }  
    
    
    public function search(Request $request)
    { 
        //dd($request);
        $keywords = [];
        $keywords = explode(",", $request->search_words);
        // キーワードの数だけループし、LIKE句の配列を作る
        $keywordCondition = [];
        foreach ($keywords as $keyword) {
            $keywordCondition[] = 'search_tag LIKE \'%' . $keyword . '%\'';
        }
        //"search_tag LIKE '%%'"
        // ここで、 
        // [ "search_tag LIKE '%hoge%'", 
        //   "search_tag LIKE '%piyo%'"]
        // という配列ができあがっている。
        
        // これをORでつなげて、文字列にする
        $keywordCondition = implode(' OR ', $keywordCondition);
        $sql = DB::table('microposts')->whereRaw($keywordCondition)->orderBy('created_at', 'desc')->paginate(8);
       
        return view('microposts.search', ['sql' => $sql]);
    }
    
    public function maps()
    {
        $microposts = DB::table('microposts')->get();
        $data=$microposts->toJson();
        // dd($data);
        return view('microposts.all_maps', ['data' => $data]);
    }
    
   
    
    
}