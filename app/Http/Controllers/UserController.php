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

class UserController extends Controller {
    public function login() {
        return view('users.login');
    }

    public function loginWithFacebook() {
        session_start();
        $config = new Config();
        $appId = $config->readConfig('app.Facebook.AppId');
        $appSecret = $config->readConfig('app.Facebook.AppSecret');
        $facebook = new FacebookClient($appId, $appSecret);

        $accessToken = $facebook->getAccessToken();
        if ($accessToken) {
            $_SESSION['user'] = $accessToken;
            $facebook->setAccessToken($accessToken);
            $userInfo = $facebook->getUser();
            $userInfo['password'] = '123456';
            $userInfo['access_token'] = $accessToken;

            $user = $this->checkFbUserExists($userInfo['facebook_uid']);
            if($user){
                return view('home');
            }else{
            $this->saveUser($userInfo);
                return view('home');
            }
        } else {
            $permissions = ['email'];
            $loginUrl = $facebook->getLoginUrl('http://laravel.example.com/user/loginFacebook', $permissions);

            return redirect($loginUrl);
        }
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