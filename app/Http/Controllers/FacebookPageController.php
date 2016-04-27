<?php
/**
 * Created by PhpStorm.
 * User: trantan
 * Date: 16/04/17
 * Time: 4:28
 */

namespace App\Http\Controllers;

use App\FacebookPage;

class FacebookPageController extends Controller {

    public function getAllFacebookPages(){
        return FacebookPage::all(array('page_id'));
    }
}