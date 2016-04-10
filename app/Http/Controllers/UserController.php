<?php
/**
 * Created by PhpStorm.
 * User: trantan
 * Date: 16/03/13
 * Time: 2:35
 */

namespace App\Http\Controllers;

use App\User;
use App\Utils\FacebookClient;
use App\Utils\Config;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function login() {
        return view('users.login');
    }

    public function loginWithFacebook() {
        session_start();
        $appId = Config::readConfig('app.Facebook.AppId');
        $appSecret = Config::readConfig('app.Facebook.AppSecret');
        $facebook = new FacebookClient($appId, $appSecret);

        $permissions = ['email'];
        $loginUrl = $facebook->getLoginUrl('http://laravel.example.com/user/callbackFacebook', $permissions);
        return redirect($loginUrl);

    }

    public function callbackFacebook(Request $request){
        session_start();
        $appId = Config::readConfig('app.Facebook.AppId');
        $appSecret = Config::readConfig('app.Facebook.AppSecret');
        $facebook = new FacebookClient($appId, $appSecret);

        $accessToken = $facebook->getAccessToken();

        if ($accessToken) {
            $facebook->setAccessToken($accessToken);
            $userInfo = $facebook->getUser();
            $userInfo['password'] = '123456';
            $userInfo['access_token'] = $accessToken;
//            $_SESSION['user'] = $userInfo['facebook_uid'];
            $request->session()->put('user',$userInfo['facebook_uid']);

            $user = $this->checkFbUserExists($userInfo['facebook_uid']);
            if($user){
                return redirect('/');
            }else{
                $this->saveUser($userInfo);
                return redirect('/');
            }
        }else{
            return redirect("/");
        }
    }

    public function logout(Request $request){
        if($request->session()->has('user')){
            $request->session()->clear();
        }
        session_start();
        session_destroy();

        return redirect("/");
    }

    public function saveUser($userInfo) {
        $user = new User();
        $user->name = $userInfo['name'];
        $user->email = $userInfo['email'];
        $user->password = $userInfo['password'];
        $user->facebook_uid = $userInfo['facebook_uid'];
        $user->access_token = $userInfo['access_token'];

        $user->save();
    }

    public function checkFbUserExists($facebook_uid){
        $user = User::where('facebook_uid',$facebook_uid)->first();

        return $user->id;
    }
}