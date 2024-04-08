<?php
namespace Ppci\Controllers;


use \Ppci\Controllers\PpciController;
use \Ppci\Config\IdentificationConfig;

class Login extends PpciController
{
    function index()
    {
        $login = new \Ppci\Libraries\Login();
        $config = new IdentificationConfig();
        if (in_array($config->identificationType, ["BDD", "LDAP", "LDAP-BDD", "CAS-BDD"])) {
            return ($login->display());
        } else {
            $this->message->set(_("Le mode d'identification dans l'application ne vous permet pas d'accéder à la page de connexion"), true);
            $defaultPage = new \Ppci\Libraries\DefaultPage();
            return ($defaultPage->display());
        }
    }
    public function LoginExec()
    {
        $login = new \Ppci\Libraries\Login();
        $config = new IdentificationConfig();
        if (!in_array($config->identificationType, ["BDD", "LDAP", "LDAP-BDD", "CAS-BDD"])) {
            return redirect()->to(site_url());
        } else {
            return redirect()->to($login->getLogin());
        }
    }
    public function disconnect()
    {
        $login = new \Ppci\Models\Login();
        $login->disconnect();
        $this->message->set(_("Vous avez été déconnecté"));
        $defaultPage = new \Ppci\Libraries\DefaultPage();
        return ($defaultPage->display());
    }
}