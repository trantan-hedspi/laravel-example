<?php

namespace App\Http\Controllers;

use App\Post;

class PostController extends Controller{

    protected  $facebook;

    public function __construct()
    {
    }
    public function index(){
        $posts = Post::all();
        return view('posts.index', ['posts'=>$posts]);
    }
}
?>