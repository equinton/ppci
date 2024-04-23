<?php
namespace Ppci\Controllers;

class Log extends PpciController
{
    function getLastConnections()
    {
        $log = service("Log");
        $log->setMessageLastConnections();
        $lib = new \Ppci\Libraries\DefaultPage();
        return ($lib->display());
    }
}
