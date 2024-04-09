<?php
namespace Ppci\Libraries;


use Config\App;
use Ppci\Models\Gacltotp;
use CodeIgniter\Cookie\Cookie;

class Login extends PpciLibrary
{
    protected $dataclass;
    function __construct()
    {
        parent::__construct();
        $this->dataclass = new \Ppci\Models\Login();
    }
    function getLogin()
    {
        try {
            $config = new App();
            $ident_type = $config->identificationMode;
            $gacltotp = new Gacltotp($this->config->privateKey, $this->config->pubKey);
            $log = service("Log");
            if (
                in_array($ident_type, ["BDD", "LDAP", "LDAP-BDD", "CAS-BDD"])
                && empty($_POST["login"])
                && empty($_SESSION["login"])
                && empty($_COOKIE["tokenIdentity"])
                && empty($_POST["cas_required"])
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
                     * Verify if the cookie totpTrustBrowser is present and valid
                     */
                    $totpNecessary = true;
                    if (isset($_COOKIE["totpTrustBrowser"])) {
                        $content = json_decode($gacltotp->decode($_COOKIE["totpTrustBrowser"],"pub"), true);
                        if ($content["uid"] == $_SESSION["login"] && $content["exp"] > time()) {
                            $totpNecessary = false;
                            $_SESSION["isLogged"] = true;
                        }
                    }
                    /**
                     * Display the form to entry the TOTP code
                     */
                    if ($totpNecessary) {
                        return "totp";
                    }
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
                        $this->dataclass->disconnect();
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
        } catch (\Exception $e) {
            $message = service("MessagePpci");
            $message->set($e->getMessage(), true);
        }
        if ($_SESSION["isLogged"]) {
            $this->log->setMessageLastConnections();
            //$this->log->setLog($_SESSION["login"], "connection", "ok");
            if ($_POST["loginByTokenRequested"] == 1){
                helper('cookie');
                    $maxAge = $config->tokenIdentityValidity;
                    $content = json_encode(["uid"=>$_SESSION["login"],
                    "exp"=>time() + $maxAge]);
                    $encoded = $gacltotp->encode($content, "priv");
                    $cookie = new Cookie(
                        'tokenIdentity',
                        $encoded,
                        [
                            'max-age' => $maxAge
                        ]
                    );
                    set_cookie($cookie);
            }
        }
        unset($_SESSION["menu"]);
        //return ("default");
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