<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Post;
use DB;
use Auth;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     * The call to auth middleware will force all requests to redirect to login
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $posts = DB::select('select * from posts');
        // $posts =  Post::all();
        // $posts =  Post::orderBy('created_at', 'desc')->get();
        $posts =  Post::orderBy('created_at', 'desc')->paginate(10);
        return view('posts.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // the rules are provided in an array along with the request
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999' // set max size to >2MB, Apache limitation
        ]);

        // Handle file upload
        if ($request->hasFile('cover_image')) {
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $ext = $request->file('cover_image')->getClientOriginalExtension();
            // make the file name unique
            $filenameToStore = $filename.'_'.time().'.'.$ext;
            // store
            $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
        } else {
            $filenameToStore = 'noimage.jpeg';
        }

        // Create Post
        $post = new Post();
        $post->user_id = Auth::user()->id;
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->cover_image = $filenameToStore;
        $post->save();

        return redirect('/posts')->with('success', 'Post Created!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);
        return view('posts.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        // verify user is owner of post
        if (Auth::user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized');
        }

        return view('posts.edit')->with('post', $post);
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
        // the rules are provided in an array along with the request
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
            'cover_image' => 'image|nullable|max:1999' // set max size to >2MB, Apache limitation
        ]);

        $post = Post::find($id);

        // verify user is owner of post
        if (Auth::user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized');
        }

        // Handle file upload: will only be assigned if a file is uploaded.
        // Else, current file won't be overwritten
        if ($request->hasFile('cover_image')) {
            $filenameWithExt = $request->file('cover_image')->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $ext = $request->file('cover_image')->getClientOriginalExtension();
            // make the file name unique
            $filenameToStore = $filename.'_'.time().'.'.$ext;
            // store
            $path = $request->file('cover_image')->storeAs('public/cover_images', $filenameToStore);
            $post->cover_image = $filenameToStore;
        }

        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->save();

        return redirect('/posts')->with('success', "Post id=$post->id Updated!");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        // verify user is owner of post
        if (Auth::user()->id !== $post->user_id) {
            return redirect('/posts')->with('error', 'Unauthorized');
        }

        // Delete image resource
        if ($post->cover_image != 'noimage.jpeg') {
            $filePath = 'public/cover_images'.$post->cover_image;
            Storage::delete($filePath);
        }

        $post->delete();
        return redirect('/posts')->with('success', "Post id=$post->id Removed!");
    }
}
