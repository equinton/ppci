<?php namespace Ppci\Controllers;
use Ppci\Config\IdentificationConfig;
class Login extends PpciController {
    function index() {
        $login = new \Ppci\Libraries\Login();
        $config = new IdentificationConfig();
        if (in_array($config->identificationType, ["BDD","LDAP","LDAP-BDD","CAS-BDD"])) {
        return ($login->display());
        } else {
            $this->message->set(_("Le mode d'identification dans l'application ne vous permet pas d'accéder à la page de connexion"), true);
            return redirect()->to(site_url());
        }
    }
}