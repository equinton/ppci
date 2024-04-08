<?php namespace Ppci\Libraries;
class PpciLibrary {
    protected $session;
    protected $message;
    protected $dataclass;
    protected $config;
    protected $log;

    function __construct() {
        $this->message = service('MessagePpci');
        $this->session = session();
        $this->init = service("PpciInit");
        $this->config = service("AppConfig");
        $this->log = service ("Log");
        $this->init::Init();
    }
}