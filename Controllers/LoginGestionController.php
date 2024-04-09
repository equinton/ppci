<?php
namespace Ppci\Controllers;


use \Ppci\Controllers\PpciController;
use \Ppci\Libraries\LoginGestionLib;

class LoginGestionController extends PpciController {

    protected $lib;
    function __construct() {
        $this->lib = new LoginGestionLib();
    }
    function index() {
        return $this->lib->index();
    }

}