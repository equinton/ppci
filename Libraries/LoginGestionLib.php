<?php
namespace Ppci\Libraries;

use \Ppci\Models\LoginGestion;
use \Ppci\Models\Acllogin;

class LoginGestionLib extends PpciLibrary
{
    function __construct()
    {
        parent::__construct();
        $this->dataClass = new LoginGestion();
    }

    function index()
    {
        $vue = service("Smarty");
        $data = $this->dataClass->getlist();
        $vue->set($data, "data");
        $vue->set("ppci/ident/loginliste.tpl", "corps");
        return $vue->send();
    }
    function change()
    {
        try {
            $data = $this->dataRead($_REQUEST["id"], "ppci/ident/loginsaisie.tpl");
            $vue = service("Smarty");
            $vue->set($this->appConfig->APP_passwordMinLength, "passwordMinLength");
            unset($data["password"]);
            /**
             * Add dbconnect_provisional_nb
             */
            if (!empty($data["login"])) {
                $data["dbconnect_provisional_nb"] = $this->dataClass->getDbconnectProvisionalNb($data["login"]);
            }
            $vue->set($data, "data");
            return $vue->send();
        } catch (\Exception $e) {
            $this->message->set($e->getMessage(), true);
            return $this->index();
        }
    }
    function write()
    {
        try {
            $id = $this->dataClass->write($_REQUEST);
            if ($id > 0) {
                /*
                 * Ecriture du compte dans la table acllogin
                 */

                $acllogin = new Acllogin();
                if (!empty($_REQUEST["nom"])) {
                    $nom = $_REQUEST["nom"] . " " . $_REQUEST["prenom"];
                } else {
                    $nom = $_REQUEST["login"];
                }
                $acllogin->addLoginByLoginAndName($_REQUEST["login"], $nom);
                return $this->index();
            }
        } catch (\Exception $e) {
            $this->message->set(_("Problème rencontré lors de l'enregistrement"), true);
            $this->message->setSyslog($e->getMessage());
            return $this->change();
        }
    }
    function delete()
    {
        try {
            $this->dataDelete($_POST["id"]);
            return $this->index();
        } catch (\Exception $e) {
            return $this->change();
        }
    }

    function changePassword()
    {
        if ($this->log->getLastConnexionType($_SESSION["login"]) == "db") {
            $vue = service("Smarty");
            $vue->set("ppci/ident/loginChangePassword.tpl", "corps");
            $vue->set($this->appConfig->APPLI_passwordMinLength, "passwordMinLength");
        } else {
            $this->message->set(_("Le mode d'identification utilisé pour votre compte n'autorise pas la modification du mot de passe depuis cette application"), true);
            defaultPage();
        }
    }
    function changePasswordExec()
    {
        if (!$this->dataClass->changePassword($_REQUEST["oldPassword"], $_REQUEST["pass1"], $_REQUEST["pass2"])) {
        } else {
            /**
             * Send mail to the user
             */
            $data = $this->dataClass->lireByLogin($_SESSION["login"]);
            if (!empty($data["mail"]) && $this->config->MAIL_enabled) {
                $dbparam = service("Dbparam");
                $subject = sprintf(_("%s - changement de mot de passe"), $dbparam->getParam("APPLI_title"));
                require_once "ppci/utils/mail.class.php";
                $mail = new Mail($this->config->MAIL_param);
                $data["APPLI_address"] = $APPLI_address;
                $data["applicationName"] = $_SESSION["APPLI_title"];
                if ($mail->SendMailSmarty($SMARTY_param, $data["mail"], $subject, "ppci/mail/passwordChanged.tpl", $data)) {
                    $log->setLog($_SESSION["login"], "password mail confirm", "ok");
                } else {
                    $log->setLog($_SESSION["login"], "password mail confirm", "ko");
                }
            }
        }
        defaultPage();
    }
}