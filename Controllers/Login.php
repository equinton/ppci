<?php namespace Ppci\Controllers;
use Ppci\Config\IdentificationConfig;
class Login extends PpciController {
    function index() {
        $config = new IdentificationConfig();
        if (in_array($config->identificationType, ["BDD","LDAP","LDAP-BDD","CAS-BDD"])) {
            $vue = service('Smarty');
            $vue->set("ppci/ident/login.tpl", "corps");
            if ($config->identificationType == "CAS-BDD") {
                $vue->set(1,"CAS_enabled");
            } else {
                $vue->set(0, "CAS_enabled");
            }
            $vue->set($config->tokenIdentityValidity, "tokenIdentityValidity");
            $vue->set($config->APPLI_lostPassword,"lostPassword");
            $vue->set("","moduleCalled");
            return $vue->send();
        } else {
            $this->message->set(_("Le mode d'identification dans l'application ne vous permet pas d'accéder à la page de connexion"), true);
            return redirect()->to(site_url());
        }

    }
}