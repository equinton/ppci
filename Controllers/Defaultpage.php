<?php
namespace Ppci\Controllers;
class Defaultpage extends \App\Controllers\BaseController
{
    public function index()
    {
        $s = service('Smarty');
        $s->set("1.0", "version");
        $s->set("09/02/2024","versiondate");
        helper('ppci');
        printA($_SERVER);
        return $s->send();
    }
}