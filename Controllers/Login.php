<?php
namespace Ppci\Controllers;


use \Ppci\Controllers\PpciController;

class Login extends PpciController
{
    function index()
    {   
        $login = new \Ppci\Libraries\Login();
        $idConfig = service("IdentificationConfig");
        if (in_array($idConfig->identificationMode, ["BDD", "LDAP", "LDAP-BDD", "CAS-BDD"])) {
            return ($login->display());
        } else {
            /**
             * Identification HEADER
             */
            if (!$_SESSION["isLogged"] && $idConfig->identificationMode == "HEADER") {
                $retour = $login->getLogin();
                if (!empty ($retour)) {
                    return redirect()->to($retour);
                } else {
                    if ($_SESSION["isLogged"]) {
                        return $this->defaultReturn();
                    } else {
                        $_SESSION["filterMessages"][]= _("Identification refusée");
                        return redirect()->to(site_url());
                    }
                }
            }
            $_SESSION["filterMessages"][]= _("Le mode d'identification dans l'application ne vous permet pas d'accéder à la page de connexion");
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
            return $this->defaultReturn($login->getLogin());
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
    protected function defaultReturn($retour = "") {
        if ($_SESSION["isLogged"]) {
            if (!empty($_SESSION["moduleRequired"])) {
                $retour = $_SESSION["moduleRequired"];
            } elseif ($retour == "login") {
                $retour = "";
            }
        }
        if (empty($retour)) {
            $lib = new \Ppci\Libraries\DefaultPage();
            return ($lib->display());
        } else {
            return redirect()->to($retour);
        }
    }
}