<?php
namespace Ppci\Config;

use CodeIgniter\Config\BaseConfig;

class Ppci extends BaseConfig
{
    public $APPLI_version = "23.0.0";
    public $APPLI_dbversion = "23.0";
    public $APPLI_versiondate = "12/12/2023";
    public $language = "fr";
    /**
     * Duration of conservation of logs in table log
     *
     * @var integer
     */
    public $LOG_duration = 365;
    public $localePath = APPPATH.'locale';
}