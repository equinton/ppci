<?php
namespace Ppci\Config;
use CodeIgniter\Config\BaseConfig;
class Rights extends BaseConfig {
    /**
     * Set 1 to disable the creation of new rights in table aclaco
     *
     * @var integer
     */
    public $GACL_disable_new_right = 1;
    /**
     * List of rights by module
     */
    protected array $rights = [
        "admin"=>["admin"]
    ];

    function getRights(string $moduleName):array {
        if (isset($this->rights[$moduleName])) {
            return $this->rights[$moduleName];
        } else {
            return [];
        }
    }
}