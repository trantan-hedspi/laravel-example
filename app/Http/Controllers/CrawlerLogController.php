<?php
/**
 * Created by PhpStorm.
 * User: trantan
 * Date: 16/04/30
 * Time: 4:07
 */

namespace App\Http\Controllers;

use App\CrawlerLogs;
use Illuminate\Support\Facades\DB;

class CrawlerLogController extends Controller {

    public function getLogByObjectIdAndType($object_id, $type){
        return CrawlerLogs::where( 'object_id', $object_id)->where('type', $type)->first();
    }

    public function addCrawlerLog($crawler_log){
        $add_crawler_log = new CrawlerLogs();
        $add_crawler_log->object_id = $crawler_log['object_id'];
        $add_crawler_log->type = $crawler_log['type'];
        $add_crawler_log->next_page = $crawler_log['next_page'];
        $add_crawler_log->previous_page = $crawler_log['previous_page'];
        $add_crawler_log->save();
    }

    public function updateCrawlerLog($object_id, $type, $previous_paging, $next_paging){
        $update_value = array(
            "next_page" => $next_paging,
            'updated_at' => 'NOW()'
        );
        if(!empty($previous_paging)){
            $update_value['previous_page'] = $previous_paging;
        }

        DB::table('crawler_logs')
            ->where('object_id', $object_id)
            ->where('type', $type)
            ->update($update_value);
    }
}