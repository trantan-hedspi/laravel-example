<?php
namespace App\Batch\Base;

use App\Utils\Log4Php;

abstract class CrawlerBatchBase {

    protected $execute_class;
    protected $facebook;
    protected $logger;

    public function __construct() {
        $this->execute_class = get_class($this);
        $this->logger = new Log4Php();
    }

    public function executeProcess() {
        try {
            $start_time = date("Y-m-d H:m:s");
            $this->doProcess();
            $end_time = date("Y-m-d H:m:s");
            $this->logger->warn("{$this->execute_class}: Executed success! start_time = {$start_time} - end_time = {$end_time}");
        } catch (Exception $e) {
            $this->logger->error("{$this->execute_class}: Executed error! start_time = {$start_time} - end_time = {$end_time}");
            $this->logger->error($e);
        }
    }

    abstract function doProcess();

}