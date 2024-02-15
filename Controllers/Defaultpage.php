<?php
namespace Ppci\Controllers;

class Defaultpage extends PpciController
{
    public function index()
    {
        $vue = service('Smarty');
        $vue->set("1.0", "version");
        $vue->set("09/02/2024", "versiondate");
        $i = $this->session->get("i");
        if (empty($i)) {
            $i = 1;
        } else {
            $i++;
        }

        $this->session->set(array("i"=> $i++));
        printA($this->session->get());
        $this->message->setSyslog("test d'erreur dans syslog", false);
        $this->message->displaySyslog();
        printA($this->message->get());
        return $vue->send();
    }
}