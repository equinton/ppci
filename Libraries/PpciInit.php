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