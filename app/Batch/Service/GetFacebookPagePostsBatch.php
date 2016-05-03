<?php
namespace App\Batch\Service;

use App\Batch\Base\CrawlerBatchBase;
use App\CrawlerLogs;
use App\Http\Controllers\CrawlerLogController;
use App\Http\Controllers\FbPagePostController;
use App\Utils\FacebookClient;
use App\Utils\Config;
use App\Http\Controllers\FacebookPageController;
use App\Http\Controllers\UserController;

class GetFacebookPagePostsBatch extends CrawlerBatchBase {
    const PAGE_LIMIT = 100;
    const FB_API_RATE_LIMIT = 600;
    const WAITING_RATE_LIMIT = 600;

    public function __construct() {
        parent::__construct();
        $this->facebook = $this->initFacebookClient();
    }

    public function initFacebookClient() {
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
        $crawler_log_controller = new CrawlerLogController();
        $facebook_pages = $facebook_page_controller->getAllFacebookPages();

        foreach ($facebook_pages as $page) {
            $crawler_log = $crawler_log_controller->getLogByObjectIdAndType($page->id, CrawlerLogs::TYPE_FB_PAGE_POST);

            if ($crawler_log && !empty($crawler_log->previous_page) && empty($crawler_log->next_page)) {
                $count = 0;
                $next_paging = null;

                while (1) {
                    $count++;
                    if ($count == 1) {
                        list($posts_data, $next_paging, $previous_paging) = $this->getNewPostsByApi($page->page_id, $crawler_log->previous_page);
                        foreach ($posts_data as $data) {
                            $this->updateFacebookPagePosts($page->page_id, $data);
                        }
                        $this->updateCrawlerLog($page->id, CrawlerLogs::TYPE_FB_PAGE_POST, $next_paging, $previous_paging);
                    } else {
                        list($posts_data, $next_paging) = $this->getOldPostsByApi($page->page_id, $next_paging);
                        foreach ($posts_data as $data) {
                            $this->updateFacebookPagePosts($page->page_id, $data);
                        }
                    }

                    if (count($posts_data) < self::PAGE_LIMIT) {
                        break;
                    }
                }
            } else {
                //Update previous paging
                list($posts_data, $next_paging, $previous_paging) = $this->getNewPostsByApi($page->page_id, null);
                foreach ($posts_data as $data) {
                    $this->updateFacebookPagePosts($page->page_id, $data);
                }
                $this->updateCrawlerLog($page->id, CrawlerLogs::TYPE_FB_PAGE_POST, null, $previous_paging);

                if(isset($crawler_log->next_page)){
                    $next_paging = $crawler_log->next_page;
                }

                while (1) {
                    list($posts_data, $next_paging) = $this->getOldPostsByApi($page->page_id, $next_paging);
                    var_dump($posts_data);
                    foreach ($posts_data as $data) {
                        $this->updateFacebookPagePosts($page->page_id, $data);
                    }
                    $this->updateCrawlerLog($page->id, CrawlerLogs::TYPE_FB_PAGE_POST, $next_paging, null);
                    if (count($posts_data) < self::PAGE_LIMIT) {
                        break;
                    }
                }
            }
        }
    }

    public function getOldPostsByApi($page_id, $next_page) {
        try {
            $params = array();
            if ($next_page) {
                list($paging_token, $until) = $this->getPagingToken($next_page);
                $params["__paging_token"] = $paging_token;
                $params["until"] = $until;
            }
            $params["limit"] = self::PAGE_LIMIT;
            $posts = $this->facebook->getByFacebookApi("/{$page_id}/posts", $params);

            $posts_data = isset($posts['data']) ? $posts['data'] : null;
            $posts_paging = isset($posts['paging']['next']) ? $posts['paging']['next']: null; //previous token

            return array($posts_data, $posts_paging);
        } catch (\Exception $e) {
            $this->logger->error("GetFacebookPagePostsBatch#getFacebookPagePostByApi: Get Old Post Error!");
            $this->logger->error($e);
        }
    }

    public function getNewPostsByApi($page_id, $previous_page) {
        try {
            $params = array();
            if ($previous_page) {
                list($paging_token, $until) = $this->getPagingToken($previous_page);
                $params["__paging_token"] = $paging_token;
                $params["since"] = $until;
            }
            $params["limit"] = self::PAGE_LIMIT;
            $posts = $this->facebook->getByFacebookApi("/{$page_id}/posts", $params);

            $posts_data = $posts['data'];
            $posts_next_paging = isset($posts['paging']['next']) ? $posts['paging']['next'] : null; //next token
            $posts_previous_paging = isset($posts['paging']['previous']) ? $posts['paging']['previous'] : null;

            return array($posts_data, $posts_next_paging, $posts_previous_paging);
        } catch (\Exception $e) {
            $this->logger->error("GetFacebookPagePostsBatch#getFacebookPagePostByApi: Get Old Post Error!");
            $this->logger->error($e);
        }
    }

    private function updateFacebookPagePosts($page_id, $data) {
        $fb_page_post_controller = new FbPagePostController();

        if (!$fb_page_post_controller->getPagePostByObjectId($data['id'])) {
            $post = array();
            $post['object_id'] = $data['id'];
            $post['fb_page_id'] = $page_id;
            $post['message'] = isset($data['message']) ? $data['message']: null;
            $post['story'] = isset($data['story']) ? $data['story']: null;
            $post['created_time'] = $data['created_time'];
            $fb_page_post_controller->addFbPagePost($post);
        } else {
            $message = isset($data['message']) ?: null;
            $story = isset($data['story']) ?: null;
            $fb_page_post_controller->updateFbPagePost($data['id'], $message, $story);
        }
    }

    private function getPagingToken($paging) {
        if (!$paging) {
            return null;
        }
        $paging_elements = explode("&", $paging);
        $token = null;
        $since_until = null;

        foreach ($paging_elements as $element) {
            if (strpos($element, "paging_token") !== false) {
                $paging_token = explode("=", $element);
                $token = $paging_token[1];
            }

            if (strpos($element, "until") !== false || strpos($element, "since") !== false) {
                $index = explode("=", $element);
                $since_until = $index[1];
            }
        }

        return array($token, $since_until);
    }

    private function updateCrawlerLog($page_id, $type, $next_paging, $previous_paging) {
        $crawler_log_controller = new CrawlerLogController();
        $check = $crawler_log_controller->getLogByObjectIdAndType($page_id, $type);

        if (isset($check)) {
            $previous_paging = $previous_paging ?: null;
            $next_paging = $next_paging ?: "";
            $crawler_log_controller->updateCrawlerLog($page_id, $type, $previous_paging, $next_paging);
        } else {
            $new_crawler_log = array();
            $new_crawler_log['object_id'] = $page_id;
            $new_crawler_log['type'] = CrawlerLogs::TYPE_FB_PAGE_POST;
            $new_crawler_log['next_page'] = $next_paging;
            $new_crawler_log['previous_page'] = $previous_paging;
            $crawler_log_controller->addCrawlerLog($new_crawler_log);
        }
    }
}