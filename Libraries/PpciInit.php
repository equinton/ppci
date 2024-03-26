<?php
namespace Ppci\Libraries;

use Config\App;
use \Ppci\Models\Ppciexception;
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
            $paramApp = new App();
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
            } catch (\Ppciexception $e) {
                $message->set($e->getMessage());
            }

            self::$isInitialized = true;
        }


    }
}