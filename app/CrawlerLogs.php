<?php
/**
 * Created by PhpStorm.
 * User: trantan
 * Date: 16/04/30
 * Time: 3:09
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class CrawlerLogs extends Model {
    const TYPE_FB_PAGE_POST = 1;

    protected $table = "crawler_logs";
}