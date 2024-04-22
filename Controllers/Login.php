<?php
namespace Ppci\Controllers;


use \Ppci\Controllers\PpciController;

class Login extends PpciController
{
    function index()
    {   
        $idConfig = service("IdentificationConfig");
        if (in_array($idConfig->identificationMode, ["BDD", "LDAP", "LDAP-BDD", "CAS-BDD"])) {
            $login = new \Ppci\Libraries\Login();
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
        $config = service("IdentificationConfig");
        if (!in_array($config->identificationMode, ["BDD", "LDAP", "CAS", "LDAP-BDD", "CAS-BDD"])) {
            return redirect()->to(site_url());
        } else {
            $retour = $login->getLogin();
            if (empty($retour)) {
                $lib = new \Ppci\Libraries\DefaultPage();
                return ($lib->display());
            } else {
                return redirect()->to($retour);
            }
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