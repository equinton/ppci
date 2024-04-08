<?php
namespace Ppci\Models;

use Config\App;

class Login
{
    public LoginGestion $loginGestion;
    public Log $log;
    public Acllogin $acllogin;
    public Aclgroup $aclgroup;
    public $message;
    private array $dacllogin;
    public string $identificationMode;
    public App $paramApp;

    function __construct()
    {

        $this->paramApp = service("AppConfig");
        $this->loginGestion = new LoginGestion();
        $this->loginGestion->setKeys($this->paramApp->privateKey, $this->paramApp->pubKey);
        $this->acllogin = new Acllogin();
        $this->aclgroup = new Aclgroup();
        $this->log = service("Log");
        $this->message = service("MessagePpci");
        $this->identificationMode = $this->paramApp->identificationMode;
    }

    function getLogin(string $type_authentification, $modeAdmin = false): ?string
    {
        $tauth = "";
        $this->loginGestion->attemptdelay = $this->paramApp->CONNECTION_blocking_duration;
        $this->loginGestion->nbattempts = $this->paramApp->CONNECTION_max_attempts;
        /**
         * web service
         */
        if ($type_authentification == "ws") {
            $tauth = "swtoken";
            if ($this->loginGestion->getLoginFromTokenWS($_REQUEST["login"], $_REQUEST["token"])) {
                $login = $_REQUEST["login"];
                $_SESSION["realIdentificationMode"] = "ws";
            }
        } elseif (isset($_COOKIE["tokenIdentity"])) {
            $tauth = "token";
            /**
             * Token identification
             */
            try {
                $token = new Token($this->paramApp->privateKey, $this->paramApp->pubKey);
                $login = $token->openToken($_COOKIE["tokenIdentity"]);
            } catch (PpciException $e) {
                $this->message->set(_("L'identification par jeton n'a pas abouti"));
                $this->message->setSyslog($e->getMessage());
                /**
                 * Destroy the token
                 */
                helper('cookie');
                setcookie('tokenIdentity', "", time() - 3600);
            }
        } elseif ($type_authentification == "HEADER") {
            $tauth = "header";
            $login = $this->getLoginFromHeader();
            $_SESSION["realIdentificationMode"] = "HEADER";
        } elseif ($type_authentification == "CAS") {
            $tauth = "cas";
            $login = $this->getLoginCas($modeAdmin);
            $_SESSION["realIdentificationMode"] = "CAS";
        } elseif ($type_authentification == "LDAP" || $type_authentification == "LDAP-BDD") {
            $tauth = "ldap";
            $login = $this->getLoginLdap($_POST["login"], $_POST["password"]);
            $_SESSION["realIdentificationMode"] = "LDAP";
            if (empty($login) && $type_authentification == "LDAP-BDD") {
                $tauth = "db";
                $login = $this->getLoginBDD($_POST["login"], $_POST["password"]);
                $_SESSION["realIdentificationMode"] = "BDD";
            }
        } elseif ($type_authentification == "BDD" || $type_authentification == "CAS-BDD") {
            $tauth = "db";
            $login = $this->getLoginBDD($_POST["login"], $_POST["password"]);
            $_SESSION["realIdentificationMode"] = "BDD";
        }
        if (!empty($login)) {
            $this->log->setlog($login, "connection-" . $tauth, "ok");
        } else {
            isset($_POST["login"]) ? $loginRequired = $_POST["login"] : $loginRequired = "unknown";
            $this->log->setlog($loginRequired, "connection-" . $tauth, "ko");
            $this->message->set(_("L'identification n'a pas abouti. Vérifiez votre login et votre mot de passe"), true);
        }
        return $login;
    }

    function getLoginFromHeader()
    {
        $ident_header_vars = $this->paramApp->ident_header_vars;
        $userparams = $this->getUserParams($ident_header_vars, $_SERVER);
        $login = $userparams["login"];
        $verify = false;
        if (!empty($login)) {
            /**
             * Verify if the login exists
             */
            $dlogin = $this->loginGestion->getFromLogin($login);
            /**
             * Verify if the login is recorded
             */
            if ($dlogin["id"] > 0) {
                if ($dlogin["actif"] == 1) {
                    $verify = true;
                }
            } else {
                /**
                 * Create if authorized the login
                 */
                if ($ident_header_vars["createUser"]) {
                    /**
                     * Verify if the structure is authorized
                     */
                    $createUser = true;
                    if (!empty($ident_header_vars["organizationGranted"])) {
                        $createUser = false;
                        if (is_array($userparams[$ident_header_vars["organization"]])) {
                            foreach ($userparams[$ident_header_vars["organization"]] as $org) {
                                if (in_array($org, $ident_header_vars["organizationGranted"])) {
                                    $createUser = true;
                                    break;
                                }
                            }
                        } else {
                            if (in_array($userparams[$ident_header_vars["organization"]], $ident_header_vars["organizationGranted"])) {
                                $createUser = true;
                            }
                        }
                    }
                    if (!$createUser) {
                        $this->log->setLog($login, "connection-header", "ko. The " . $userparams[$ident_header_vars["organization"]] . " is not authorized to connect to this application or the code of organization is not furnished");
                    }
                    if ($createUser) {
                        $dlogin = array(
                            "id" => 0,
                            "login" => $login,
                            "actif" => 0
                        );
                        if (!empty($userparams["groupAttribute"]) && !empty($ident_header_vars["groupsGranted"])) {
                            if (is_array($userparams["groupAttribute"])) {
                                foreach ($userparams["groupAttribute"] as $group) {
                                    if (in_array($group, $ident_header_vars["groupsGranted"])) {
                                        $dlogin["actif"] = 1;
                                        $verify = true;
                                        break;
                                    }
                                }
                            } else {
                                if (in_array($userparams["groupAttribute"], $ident_header_vars["groupsGranted"])) {
                                    $dlogin["actif"] = 1;
                                    $verify = true;
                                }
                            }
                        }
                        $login_id = $this->loginGestion->ecrire($dlogin);
                        if ($login_id > 0) {
                            $this->updateLoginFromIdentification($login, $userparams);
                            if (!$verify) {
                                /**
                                 * Send mail to administrators
                                 */
                                $APPLI_address = base_url();
                                $dbparam = service("Dbparam");
                                $subject = $dbparam->params["APP_title"] . " - " . _("Nouvel utilisateur");
                                $template = "ppci/mail/newUser.tpl";
                                $data = array(
                                    "login" => $login,
                                    "name" => $this->dacllogin["logindetail"],
                                    "appName" => $dbparam->params["APP_title"],
                                    "organization" => $userparams[$ident_header_vars["organization"]],
                                    "link" => $APPLI_address
                                );
                                $this->log->sendMailToAdmin($subject, $template, $data, "loginCreateByHeader", $login);
                                $this->message->set(_("Votre compte a été créé, mais est inactif. Un mail a été adressé aux administrateurs pour son activation"), true);
                            }
                        } else {
                            $verify = false;
                        }
                    }
                }
            }
        }
        if ($verify) {
            return $login;
        }
    }

    function getUserParams(array $attributes, array $provider = array()): array
    {
        $params = array();
        foreach ($attributes as $k => $v) {
            if (!empty($v) && isset($provider[$v])) {
                $params[$k] = $provider[$v];
            }
        }
        return $params;
    }
    /**
     * Update records of identification with data provided by the authentificator
     *
     * @param string $login
     * @param array $params
     * @return void
     */
    function updateLoginFromIdentification(string $login, array $params)
    {
        /**
         * Update logingestion
         */
        $dlogin = $this->loginGestion->getFromLogin($login);
        if ($dlogin["id"] > 0) {
            if (!empty($params["lastname"])) {
                $dlogin["nom"] = ucwords(strtolower($params["lastname"]));
                $dlogin["prenom"] = ucwords(strtolower($params["firstname"]));
            } else if (!empty($params["name"])) {
                $dlogin["nom"] = ucwords(strtolower($params["name"]));
            }
            if (!empty($params["mail"])) {
                $dlogin["mail"] = strtolower($params["mail"]);
            }
            $this->loginGestion->ecrire($dlogin);
        }
        /**
         * Update or create acllogin
         */
        $dacllogin = $this->acllogin->getFromLogin($login);
        if (empty($dacllogin["acllogin_id"])) {
            $dacllogin["acllogin_id"] = 0;
            $dacllogin["login"] = $login;
            $id = 0;
        } else {
            $id = $dacllogin["acllogin_id"];
        }
        if (!empty($params["lastname"]) && !empty($params["firstname"])) {
            $dacllogin["logindetail"] = ucwords(strtolower($params["lastname"] . " " . $params["firstname"]));
        } else if (!empty($params["name"])) {
            $dacllogin["logindetail"] = ucwords(strtolower($params["name"]));
        } else if (empty($dacllogin["logindetail"])) {
            $dacllogin["logindetail"] = $login;
        }
        $id = $this->acllogin->ecrire($dacllogin);
        $this->dacllogin = $dacllogin;
        /**
         * Add acllogin to the main group, if exists
         */
        if (!empty($params["groupeAttribute"])) {
            if (!is_array($params["groupeAttribute"])) {
                $params["groupeAttribute"] = array($params["groupeAttribute"]);
            }
            foreach ($params["groupeAttribute"] as $group) {
                $dgroups = $this->aclgroup->getGroupFromName($group);
                foreach ($dgroups as $dgroup) {
                    $this->aclgroup->addLoginToGroup($dgroup["aclgroup_id"], $id);
                }
            }
        }
    }

    /**
     * Get login from CAS server
     *
     * @param boolean $modeAdmin
     * @return string|null
     */
    public function getLoginCas($modeAdmin = false)
    {

        global $CAS_address, $CAS_port, $CAS_address, $CAS_CApath, $CAS_debug, $CAS_uri, $user_attributes;
        if ($this->paramApp->CAS_debug) {
            \phpCAS::setDebug(WRITEPATH . "logs/cas.log");
            \phpCAS::setVerbose(true);
        }
        \phpCAS::client(
            CAS_VERSION_2_0,
            $this->paramApp->CAS_address,
            $this->paramApp->CAS_port,
            $this->paramApp->CAS_uri,
            "https://" . $_SERVER["HTTP_HOST"],
            false
        );
        if (!empty($this->paramApp->CAS_CApath)) {
            \phpCAS::setCasServerCACert($this->paramApp->CAS_CApath);
        } else {
            \phpCAS::setNoCasServerValidation();
        }
        if ($modeAdmin) {
            \phpCAS::renewAuthentication();
        } else {
            \phpCAS::forceAuthentication();
        }

        $user = \phpCAS::getUser();
        if (!empty($user)) {
            $_SESSION["CAS_attributes"] = \phpCAS::getAttributes();
            if (!is_array($_SESSION["CAS_attributes"])) {
                $_SESSION["CAS_attributes"] = array($_SESSION["CAS_attributes"]);
            }
            if (!empty($_SESSION["CAS_attributes"])) {
                $params = $this->getUserParams($user_attributes, $_SESSION["CAS_attributes"]);
                $this->updateLoginFromIdentification($user, $params);
            }
        }
        return $user;
    }

    public function getLoginLdap($login, $password)
    {
        $LDAP = $this->paramApp->LDAP;
        $loginOk = "";
        if (!empty($login) && !empty($password)) {
            $login = str_replace(
                array('\\', '*', '(', ')', ),
                array('\5c', '\2a', '\28', '\29', ),
                $login
            );
            for ($i = 0; $i < strlen($login); $i++) {
                $char = substr($login, $i, 1);
                if (ord($char) < 32) {
                    $hex = dechex(ord($char));
                    if (strlen($hex) == 1) {
                        $hex = '0' . $hex;
                    }
                    $login = str_replace($char, '\\' . $hex, $login);
                }
            }
            $ldap = @ldap_connect($LDAP["address"], $LDAP["port"]);
            /**
             * Set options
             */
            if (!isset($LDAP["timeout"])) {
                $LDAP["timeout"] = 2;
            }
            ldap_set_option($ldap, LDAP_OPT_NETWORK_TIMEOUT, $LDAP["timeout"]);
            ldap_set_option($ldap, LDAP_OPT_TIMELIMIT, $LDAP["timeout"]);
            ldap_set_option($ldap, LDAP_OPT_TIMEOUT, $LDAP["timeout"]);
            if (!$ldap) {
                throw new PpciException(_("Impossible de se connecter au serveur LDAP"));
            }
            if ($LDAP["v3"]) {
                ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            }
            if ($LDAP["tls"]) {
                ldap_start_tls($ldap);
            }

            /**
             * Pour OpenLDAP et Active Directory, "bind rdn" de la forme : user_attrib=login,basedn
             *     avec généralement user_attrib=uid pour OpenLDAP,
             *                    et user_attrib=cn pour Active Directory
             * Pour Active Directory aussi, "bind rdn" de la forme : login@upn_suffix
             * D'où un "bind rdn" de la forme générique suivante :
             *     (user_attrib=)login(@upn_suffix)(,basedn)
             */
            $user_attrib_part = !empty($LDAP["user_attrib"]) ? $LDAP["user_attrib"] . "=" : "";
            $upn_suffix_part = !empty($LDAP["upn_suffix"]) ? "@" . $LDAP["upn_suffix"] : "";
            $basedn_part = !empty($LDAP["basedn"]) ? "," . $LDAP["basedn"] : "";
            //     (user_attrib=)login(@upn_suffix)(,basedn)
            $dn = $user_attrib_part . $login . $upn_suffix_part . $basedn_part;
            if (@ldap_bind($ldap, $dn, $password)) {
                $loginOk = $login;
            }
        }
        return $loginOk;
    }

    /**
     * Verify the login from the database
     *
     * @param string $login
     * @param string $password
     * @return string|null
     */
    function getLoginBDD($login, $password)
    {
        if ($this->loginGestion->controlLogin($login, $password)) {
            return strtolower($login);
        }
    }

    /**
     * Disconnect the user
     *
     * @param string $adresse_retour
     * @return void
     */
    public function disconnect($adresse_retour = "")
    {

        // Si vous voulez détruire complètement la session, effacez également
        // le cookie de session.
        // Note : cela détruira la session et pas seulement les données de session !
        helper("cookie");
        if (isset($_COOKIE[session_name()])) {
            set_cookie(session_name(), '', time() - 42000, );
        }
        /*
         * Suppression du cookie d'identification
         */
        if (isset($_COOKIE["tokenIdentity"])) {
            set_cookie("tokenIdentity", '', time() - 42000, "/");
        }
        // Finalement, on détruit la session.
        session()->destroy();
        $_SESSION = array();
        if ($this->identificationMode == "CAS") {
            \phpCAS::client(
                CAS_VERSION_2_0,
                $this->paramApp->CAS_address,
                $this->paramApp->CAS_port,
                $this->paramApp->CAS_uri,
                "https://" . $_SERVER["HTTP_HOST"]
            );
            if (!empty($CAS_CApath)) {
                \phpCAS::setCasServerCACert($CAS_CApath);
            } else {
                \phpCAS::setNoCasServerValidation();
            }
            \phpCAS::logout(array("url" => "https://" . $_SERVER["HTTP_HOST"]));

        }
    }
}
