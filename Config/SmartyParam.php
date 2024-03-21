<?php
namespace Ppci\Config;
use CodeIgniter\Config\BaseConfig;

class SmartyParam extends BaseConfig
{

    public static $templateDir = [ROOTPATH.'app/Views/templates',ROOTPATH.'vendor/equinton/ppci/Views/templates'];
    public static $compileDir = 'writable/templates_c';

}