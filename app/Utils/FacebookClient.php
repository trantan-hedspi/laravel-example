<?php
/**
 * Created by PhpStorm.
 * User: trantan
 * Date: 16/03/07
 * Time: 2:43
 */

namespace App\Utils;

use Facebook\Facebook;
use Facebook\PersistentData\FacebookSessionPersistentDataHandler;

class FacebookClient {
    protected $facebook;

    public function __construct($appId, $appSecret) {
        $this->facebook = new Facebook([
            'app_id' => $appId,
            'app_secret' => $appSecret,
            'default_graph_version' => 'v2.5',
            'persistent_data_handler' => new FacebookSessionPersistentDataHandler()
        ]);
    }

    public function setAccessToken($accessToken) {
        $this->facebook->setDefaultAccessToken($accessToken);
    }

    public function getAccessToken() {
        $helper = $this->facebook->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();

        return $accessToken;
    }

    public function getLoginUrl($redirectUrl, $permission) {
        $helper = $this->facebook->getRedirectLoginHelper();
        $loginUrl = $helper->getLoginUrl($redirectUrl, $permission);

        return $loginUrl;
    }

    public function getUser(){
        $response = $this->facebook->get('/me?fields=id,name,email');
        $userInfo = array();
        $userInfo['name'] = $response->getGraphObject()->getProperty('name');
        $userInfo['facebook_uid'] = $response->getGraphObject()->getProperty('id');
        $userInfo['email'] = $response->getGraphObject()->getProperty('email');

        return $userInfo;
    }
}