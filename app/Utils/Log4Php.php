<?php namespace App\Utils;

use Logger;

class Log4Php {

    protected $logger;

    public function __construct() {
        Logger::configure($this->initConfig());
        $this->logger = new Logger("system_log");
    }

    public function error($message){
        $this->logger->error($message);
    }

    public function warn($message){
        $this->logger->warn($message);
    }

    public function info($message){
        $this->logger->info($message);
    }

    public function initConfig(){
        $today = date("Y-m-d");
        $file_name = "trantan_system_log_".$today.".log";
        return array(
            'rootLogger' => array(
                'appenders' => array('default'),
            ),
            'appenders' => array(
                'default' => array(
                    'class' => 'LoggerAppenderFile',
                    'layout' => array(
                        'class' => 'LoggerLayoutSimple'
                    ),
                    'params' => array(
                        'file' => '/var/log/'.$file_name,
                        'append' => true
                    )
                )
            )
        );
    }

}