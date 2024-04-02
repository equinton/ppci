<?php
namespace Ppci\Libraries;

use Config\App;
use \Ppci\Models\Log;
use \Ppci\Models\PpciException;
use \Ppci\Models\Dbversion;

class PpciInit
{
    protected static $isInitialized = false;
    protected static $isDbversionOk = false;
    static function init()
    {
        if (!self::$isInitialized) {
            /**
             * Start the session
             */
            $session = session();
            /**
             * Add generic functions
             */
            helper('ppci');
            /**
             * Add messages to user and syslog
             */
            $message = service('MessagePpci');
            /**
             * Set default parameters
             */
            $session->set(
                array(
                    "APPLI_code" => "Ppci"
                )
            );
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
                            $appConfig->$key[$k]=$v;
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
                    $lenguage = $_COOKIE["locale"];
                } else {    
                    /*
                     * Recuperation de la langue du navigateur
                     */
                    $lenguage = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                    $lenguage = substr($lenguage[0], 0, 2);
                }
                /*
                 * Mise a niveau du langage
                 */
                if (in_array($lenguage, array("fr","en", "us"))) {
                    $locale->setLocale($lenguage);
                }  
            }
            /**
             * set the connection
             */
            $db = db_connect();
            $db->query("set search_path = " . $_ENV["database.default.searchpath"]);
            /**
             * Verify the database version
             */
            $dbversion = new Dbversion();
            $paramApp = service("AppConfig");
            try {
                if ($dbversion->verifyVersion($paramApp->dbversion)) {
                    self::$isDbversionOk = true;
                } else {
                    $message->set(
                        sprintf(
                            _('La base de données n\'est pas dans la version attendue (%1$s). Version actuelle : %2$s'),
                            $paramApp->dbversion,
                            $dbversion->getLastVersion()["dbversion_number"]
                        ),
                        true
                    );
                }
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
                    set_cookie("locale",$language,31536000);
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