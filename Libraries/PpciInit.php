<?php

namespace Ppci\Libraries;

use Config\App;
use \Ppci\Models\Log;
use \Ppci\Libraries\PpciException;
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
            /**
             * @var App
             */
            $appConfig = service("AppConfig");
            /**
             * @var App
             */
            $identConfig = service("IdentificationConfig");
            /**
             * @var App
             */
            $dbConfig = service("Database");
            if (is_file($appConfig->paramIniFile)) {
                $params = parse_ini_file($appConfig->paramIniFile, true);
                foreach ($params as $key => $value) {
                    $paramType = "app";
                    if (isset($identConfig->$key)) {
                        $paramType = "ident";
                    } elseif (isset($dbConfig->$key)) {
                        $paramType = "db";
                    }
                    if (is_array($value)) {
                        foreach ($value as $k => $v) {
                            if ($paramType == "app") {
                                $appConfig->$key[$k] = $v;
                            } elseif ($paramType == "ident") {
                                $identConfig->$key[$k] = $v;
                            } elseif ($paramType == "db") {
                                $dbConfig->$key[$k] = $v;
                            }
                        }
                    } else {
                        if ($paramType == "app") {
                            $appConfig->$key = $value;
                        } elseif ($paramType == "ident") {
                            $identConfig->$key = $value;
                        } elseif ($paramType == "db") {
                            $dbConfig->$key = $value;
                        }
                    }
                }
            }
            try {
                /**
                 * Set the locale
                 */
                if (!isset($_SESSION["locale"])) {
                    $locale = new Locale();
                    if (isset($_COOKIE["locale"])) {
                        $language = $_COOKIE["locale"];
                    } else {
                        /**
                         * Recuperation de la langue du navigateur
                         */
                        $language = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                        $language = substr($language[0], 0, 2);
                    }
                    $locale->setLocale($language);
                }

                /**
                 * @var Database
                 */
                $paramDb = config("Database");
                /**
                 * set the connection
                 */
                $db = db_connect();
                $db->query("set search_path = " . $paramDb->default["searchpath"]);
                /**
                 * purge logs
                 */
                if (!isset($_SESSION["log_purged"]) || !$_SESSION["log_purged"]) {
                    /**
                     * @var Log
                     */
                    $log = service('Log');
                    $log->purge($appConfig->logDuration);
                    $_SESSION["log_purged"] = true;
                }
            } catch (PpciException $e) {
                $message->set($e->getMessage());
            }

            self::$isInitialized = true;
        }
    }
}
