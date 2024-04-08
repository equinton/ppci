<?php
namespace Ppci\Controllers;
use App\Controllers\BaseController;

class Defaultpage extends BaseController
{

    
    public function index()
    {

        /*$vue = service('Smarty');
        $i = $this->session->get("i");
        if (empty($i)) {
            $i = 1;
        } else {
            $i++;
        }
        printA(_("Requêtes SQL"));
        $this->session->set(array("i"=> $i++));
        printA($this->session->get());
        $this->message->setSyslog("test d'erreur dans syslog", true);
        // $this->message->displaySyslog();
        printA($this->message->get());
        printA("Variables d'environnement");
        printA($_ENV);
        echo "<br>";
        printA("Variables de session :");
        printA($_SESSION);
        echo "<br>";
        $config = new IdentificationConfig();
        printA($config->organizationsGranted);
        printA($config->identificationType);
        return $vue->send();
        */
        $lib = new \Ppci\Libraries\DefaultPage();
        return ($lib->display());
    }
}