<?php
namespace Ppci\Controllers;
use CodeIgniter\Security\Security;
use Ppci\Config\IdentificationConfig;

class Defaultpage extends PpciController
{
    public function index()
    {
        $vue = service('Smarty');
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
        printA("Variables de session :");
        printA($_SESSION);

        $config = new IdentificationConfig();
        printA($config->organizationsGranted);
        $security = service("Security");
        $security->generateHash();
        printA(csrf_field());
        printA(csrf_token().":".csrf_hash());
        return $vue->send();
    }
}