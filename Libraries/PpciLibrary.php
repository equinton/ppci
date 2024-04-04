<?php namespace Ppci\Libraries;
class PpciLibrary {
    protected $session;
    protected $message;
    protected $dataclass;

    function __construct() {
        $this->message = service('MessagePpci');
        $this->session = session();
        $this->init = service("PpciInit");
        $this->init::Init();
    }
}