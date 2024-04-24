<?php
namespace Ppci\Libraries;

use Config\App;
use \Ppci\Models\Log;
use \Ppci\Models\PpciException;
use \Ppci\Models\Dbversion;
use App\Libraries\BeforeSession;

class PpciInit
{
    protected static $isInitialized = false;
    protected static $isDbversionOk = false;
    static function init()
    {
        if (!self::$isInitialized) {
            /**
             * Before session
             */
            if (class_exists("App\Libraries\BeforeSession")) {
                BeforeSession::index();
            }
            /**
             * Start the session
             */
            session();
            /**
             * Add generic functions
             */
            helper('ppci');
            /**
             * Add messages to user and syslog
             */
            $message = service('MessagePpci');
            if ($_ENV["CI_ENVIRONMENT"] == "development") {
                $message->displaySyslog();
            }
            /**
             * Add filter messages
             */
            if (isset($_SESSION["filterMessages"])) {
                foreach ($_SESSION["filterMessages"] as $mes) {
                    $message->set($mes, true);
                }
                unset($_SESSION["filterMessages"]);
            }

            /**
             * Get parameters stored in ini file
             * and populate App/Config/App class
             */
            $appConfig = service("AppConfig");
            $appConfig->setParameters();
            if (is_file($appConfig->paramIniFile)) {
                $params = parse_ini_file($appConfig->paramIniFile, true);
                foreach ($params as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            $appConfig->$key[$k] = $v;
                        }
                    } else {
                        $appConfig->$key = $value;
                    }
                }
            }
            /**
             * Set the locale
             */
            $locale = service("Locale");
            if (isset($_SESSION["locale"]) && $_ENV["CI_ENVIRONMENT"] != "development") {
                $locale->setLocale($_SESSION["locale"]);
            } else {
                /*
                 * Recuperation le cas echeant du cookie
                 */
                if (isset($_COOKIE["locale"])) {
                    $language = $_COOKIE["locale"];
                } else {
                    /*
                     * Recuperation de la langue du navigateur
                     */
                    $language = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    $language = substr($language[0], 0, 2);
                }
                /*
                 * Mise a niveau du langage
                 */
                if (in_array($language, array("fr", "en", "us"))) {
                    $locale->setLocale($language);
                }
            }
            try {
                $paramApp = service("AppConfig");
                /**
                 * set the connection
                 */
                /*$db = db_connect();
                $db->query("set search_path = " . $_ENV["database.default.searchpath"]);
                */
                /**
                 * Set locale parameters
                 */
                if (isset($_SESSION["locale"])) {
                    $language = $_SESSION["locale"];
                } else {
                    if (isset($_COOKIE["locale"])) {
                        $language = $_COOKIE["locale"];
                    } else {
                        /*
                         * Recuperation de la langue du navigateur
                         */
                        $language = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                        $language = substr($language[0], 0, 2);
                    }
                }
                if (in_array($language, $paramApp->languages)) {
                    $locale = service('Locale');
                    $locale->setLocale($language);
                    helper('cookie');
                    set_cookie("locale", $language, 31536000);
                }

                /**
                 * purge logs
                 */
                if (!isset($_SESSION["log_purged"]) || !$_SESSION["log_purged"]) {
                    $log = service('Log');
                    $log->purge($paramApp->logDuration);
                    $_SESSION["log_purged"] = true;
                }
            } catch (PpciException $e) {
                $message->set($e->getMessage());
            }

            self::$isInitialized = true;
        }
    }
}