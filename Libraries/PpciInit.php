<?php
namespace Ppci\Libraries;   
class PpciInit {
    protected static $isInitialized = false;
    static function init() {
        if (!self::$isInitialized) {
            /**
             * set the connection
             */
            $db = db_connect();
            $db->query("set search_path = ".$_ENV["database.default.searchpath"]);
            $db->query("select count(*) from project");
            self::$isInitialized = true;
        }

        
    }
}