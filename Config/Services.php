<?php

namespace Ppci\Config;

use CodeIgniter\Config\BaseService;

use Config\App;
use Ppci\Libraries\Locale;
use Ppci\Libraries\MessagePpci;
use Ppci\Libraries\SmartyPpci;
use Ppci\Libraries\PpciInit;
use Ppci\Models\Log;
use Ppci\Models\Dbparam;
use Ppci\Libraries\BinaryView;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */


    public static function MessagePpci($getShared = true)
    {
        return ($getShared === true ? static::getSharedInstance('MessagePpci') : new MessagePpci());
    }

    public static function PpciInit($getShared = true)
    {
        return ($getShared === true ? static::getSharedInstance('PpciInit') : new PpciInit());
    }
    public static function Log($getShared = true) {
        return ($getShared === true ? static::getSharedInstance('Log') : new Log());
    }
    public static function Locale($getShared = true) {
        return ($getShared === true ? static::getSharedInstance('Locale') : new Locale());
    }
    public static function Dbparam($getShared = true) {
        return ($getShared === true ? static::getSharedInstance('Dbparam') : new Dbparam());
    }
    public static function AppConfig($getShared = true) {
        return ($getShared === true ? static::getSharedInstance('AppConfig') : new App());
    }

    /**
     * Views
     */
        public static function Smarty($getShared = true)
    {
        return ($getShared === true ? static::getSharedInstance('Smarty') : new SmartyPpci());

    }

    public static function BinaryView($getShared = true)
    {
        return ($getShared === true ? static::getSharedInstance('BinaryView') : new BinaryView());
        
    }
}