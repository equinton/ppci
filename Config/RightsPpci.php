<?php
namespace Ppci\Config;
/**
 * List of necessary rights to run modules
 */
class RightsPpci {
    protected array $rights = [];
    protected array $adminModules = [];
    /**
     * Get the rights necessary to run a module
     *
     * @param string $moduleName
     * @return array
     */
    function getRights(string $moduleName):array {
        if (isset($this->rights[$moduleName])) {
            return $this->rights[$moduleName];
        } else {
            return [];
        }
    }
    function isAdminRequired(string $moduleName):bool {
        return in_array($moduleName, $this->adminModules);
    }
}