<?php

namespace App\Http\Controllers;

use App\Post;
use Facebook\Facebook;
use App\Utils\Config;

class PostController extends Controller{

    protected  $facebook;

    public function __construct()
    {
        $this->facebook = new Facebook([
            'app_id' => '947858151951640',
            'app_secret' => '2ae9fcd9963f371d77ddf07706dc7ae8',
//            'default_graph_version' => 'v2.5',
        ]);
    }
    public function index(){
        $posts = Post::all();
        return view('posts.index', ['posts'=>$posts]);
    }
    public function getFacebookApi(){
        $permissions = ['email'];
        $helper = $this->facebook->getRedirectLoginHelper();
        try{
            $acessToken = $helper->getAccessToken();
            if(isset($acessToken)){
                var_dump($acessToken);
            }else{
                $loginUrl = $helper->getLoginUrl('http://laravel.example.com/',$permissions);
                var_dump($loginUrl);
            }
        }catch(FacebookResponseException $e){
            echo "Graph returned an error: ".$e->getMessage();
            exit;
        }
    }

    public function readYaml(){
        $config = new Config();
        $value =  $config->readConfig('app.Facebook.AppId');
        var_dump($value);
    }
}
?>