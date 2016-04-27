<?php
namespace App\Batch\Service;

use App\Batch\Base\CrawlerBatchBase;
use App\Utils\FacebookClient;
use App\Utils\Config;
use App\Http\Controllers\FacebookPageController;
use App\Http\Controllers\UserController;

class GetFacebookPagePostsBatch extends CrawlerBatchBase
{
    const PAGE_LIMIT = 100;
    const FB_API_RATE_LIMIT = 600;
    const WAITING_RATE_LIMIT = 600;

    public function __construct()
    {
        parent::__construct();
        $this->facebook = $this->initFacebookClient();
    }

    public function initFacebookClient(){
        $appId = Config::readConfig('app.Facebook.AppId');
        $appSecret = Config::readConfig('app.Facebook.AppSecret');
        $facebook = new FacebookClient($appId, $appSecret);

        $crawler_user_id = Config::readConfig('app.CrawlerUser.UserId');
        $user_controller = new UserController();
        $user = $user_controller->getUserById($crawler_user_id);
        $facebook->setAccessToken($user->access_token);

        return $facebook;
    }

    public function doProcess() {
        $facebook_page_controller = new FacebookPageController();
        $facebook_pages = $facebook_page_controller->getAllFacebookPages();
        foreach ($facebook_pages as $page){
            $page_id = $page->page_id;
            $this->getFacebookPagePostByApi($page_id);
        }
    }

    public function getOldPostsByApi($page_id, $next_page) {
        try{
            $params = array();
            if($next_page){
                list($paging_token, $until) = $this->getPagingToken($next_page);
                $params["__paging_token"] = $paging_token;
                $params["until"] = $until;
            }
            $params["limit"] = self::PAGE_LIMIT;

            $posts = $this->facebook->getFacebookPagePosts($page_id, $params);
        }catch (\Exception $e){
            $this->logger->error("GetFacebookPagePostsBatch#getFacebookPagePostByApi: Get Old Post Error!");
            $this->logger->error($e);
        }
    }

    private function getPagingToken($paging){
        if(!$paging){
            return null;
        }
        $paging_elements = explode("&",$paging);
        $token = null;
        $since_until = null;

        foreach ($paging_elements as $element){
            if(strpos($element, "paging_token") !== false) {
                $paging_token = explode("=",$element);
                $token = $paging_token[1];
            }

            if(strpos($element, "until") !== false || strpos($element, "since") !== false){
                $index = explode("=", $element);
                $since_until = $index[1];
            }
        }

        return array($token, $since_until);
    }
}