<?php
/**
 * Created by PhpStorm.
 * User: trantan
 * Date: 16/04/30
 * Time: 4:06
 */

namespace App\Http\Controllers;

use App\FbPagePosts;
use Illuminate\Support\Facades\DB;

class FbPagePostController extends Controller {

    public function getPagePostByObjectId($object_id){
        return FbPagePosts::where('object_id',$object_id)->first();
    }

    public function addFbPagePost($post) {
        $add_post = new FbPagePosts();
        $add_post->object_id = $post['object_id'];
        $add_post->fb_page_id = $post['fb_page_id'];
        $add_post->message = $post['message'];
        $add_post->story = $post['story'];
        $add_post->created_time = $post['created_time'];
        $add_post->save();
    }

    public function updateFbPagePost($object_id, $message, $story) {
        DB::table("fb_page_posts")
            ->where('object_id', $object_id)
            ->update(['message'=> $message, 'story' => $story]);

    }
}