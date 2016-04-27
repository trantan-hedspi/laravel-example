<?php
use App\Batch\Service\GetFacebookPagePostsBatch;
include "../autoload.php";

$obj = new GetFacebookPagePostsBatch();
$obj->doProcess();