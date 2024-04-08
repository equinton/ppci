<?php
namespace Ppci\Libraries;

use Ppci\Config\IdentificationConfig;

class Login extends PpciLibrary
{
    protected $dataclass;
    function getLogin()
    {
        $login = new \Ppci\Models\Login();
        $config = new IdentificationConfig();
        $ident_type = $config->identificationType;
        $log = service("Log");
        if (
            in_array($ident_type, ["BDD", "LDAP", "LDAP-BDD", "CAS-BDD"])
            && empty($_REQUEST["login"])
            && empty($_SESSION["login"])
            && empty($_COOKIE["tokenIdentity"])
            && empty($_REQUEST["cas_required"])
        ) {
            return "login";
        } else {
            /**
             * Verify the login
             */
            if (!isset($_SESSION["login"])) {
                if (!empty($_REQUEST["token"]) && !empty($_REQUEST["login"])) {
                    $ident_type = "ws";
                }
                /**
                 * For CAS-BDD
                 */
                if ($_REQUEST["cas_required"] == 1 || !empty($_REQUEST["ticket"])) {
                    $ident_type = "CAS";
                    $_SESSION["cas_required"] = 1;
                }
                $_SESSION["login"] = strtolower($this->dataclass->getLogin($ident_type, false));
            }
        }
        if (isset($_SESSION["login"])) {
            unset($_SESSION["cas_required"]);
            /**
             * Verify if the double authentication is mandatory
             */
            $acllogin = new \Ppci\Models\Acllogin();
            if ($acllogin->isTotp() && !isset($_COOKIE["tokenIdentity"]) && !isset($_POST["otpcode"])) {
                /**
                 * Display the form to entry the TOTP code
                 */
                return "totp";
            } else {
                /**
                 * Verify that the login used as admin is the same as login
                 */
                if (isset($_POST["loginAdmin"]) && $_POST["login"] != $_SESSION["login"]) {
                    $log->setLog(
                        $_SESSION["login"],
                        "admin-reauthenticate",
                        "Error: the used account (" . $_POST["login"] . ") is not the same of the current account"
                    );
                    $login->disconnect();
                } else {
                    $_SESSION["isLogged"] = true;
                }
            }
        } else {
            if ($ident_type == "ws") {
                /*http_response_code(401);
                $vue->set(array("error_code" => 401, "error_message" => _("Identification refusée")));*/
            } else {
                return "login";
            }
        }
    }

    public function display()
    {
        $vue = service('Smarty');
        $vue->set("ppci/ident/login.tpl", "corps");
        if ($this->config->identificationMode == "CAS-BDD") {
            $vue->set(1, "CAS_enabled");
        } else {
            $vue->set(0, "CAS_enabled");
        }
        $vue->set($this->config->tokenIdentityValidity, "tokenIdentityValidity");
        $vue->set($this->config->APPLI_lostPassword, "lostPassword");
        $vue->set("", "moduleCalled");
        return $vue->send();
    }
}