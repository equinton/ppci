<?php
namespace Ppci\Config;

use CodeIgniter\Config\BaseConfig;

class Ppci extends BaseConfig
{
    public $APPLI_version = "23.0.0";
    public $APPLI_dbversion = "23.0";
    public $APPLI_versiondate = _("12/12/2023");
    public $language = "fr";
    /**
     * Duration of conservation of logs in table log
     *
     * @var integer
     */
    public $LOG_duration = 365;
    /**
     * Keys used to encrypt data in database or generate tokens
     *
     * @var string
     */
    public $privateKey = "param/id_metabo";
    public $pubKey = "param/id_metabo.pub";
}