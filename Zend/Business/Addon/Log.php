<?php

class Business_Addon_Log extends Business_Abstract
{
    private $logger;
    private $writer;
    private static $_instance;
    private $path;
    private $struct = "%s\t%s\t%s";
    const APP_UPDATE=1; //cap nhat thong tin khuyen mai trong admin app ban hang
    const APP_ADD=2; //them thong tin khuyen mai trong admin app ban hang
    const APP_UPDATE_PRE=3; //them thong tin khuyen mai trong admin app ban hang
    
    function __construct($logName) {
        $logConfig = Globals::getConfig("log");
            
        if ($logConfig==null){
            $this->path = "php://output";
        } else {
            $list = $logConfig->list;
            if ($list == null) {
                $this->path = "php://output";
            } else {
                $lists = explode(",",$list);
                    
                if (in_array($logName, $lists)) {
                    $filename =  $logConfig->$logName->filename;
    
                    if ($filename!=null) {
                        $subfix = date("d-m-Y");
                        $this->path = APPLICATION_PATH . "/logs/" . $filename . "." . $subfix . ".txt";
                    } else {
                        $this->path = "php://output";
                    }
                }
            }
        }
            
        $this->logger = new Zend_Log();
        $this->writer = new Zend_Log_Writer_Stream($this->path);

        $this->logger->addWriter($this->writer);
    }

    //public static function
    /**
     * get instance of Business_Addon_Log
     *
     * @return Business_Addon_Log
     */
    public static function getInstance($logName) {
        if (self::$_instance == null) {
            self::$_instance = new self($logName);
        }
        return self::$_instance;
    }
    
    public function writeInfo($action, $msg) {
        $_msg = sprintf($this->struct, $action, $msg, date("d/m/y H:i:s"));
        $this->logger->log($_msg, Zend_Log::INFO);
    }
    public function writeError($action, $msg) {
        $_msg = sprintf($this->struct, $action, $msg, date("d/m/y H:i:s"));
        $this->logger->log($_msg, Zend_Log::ERR);
    }
}

?>