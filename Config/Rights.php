<?php
namespace Ppci\Config;

/**
 * List of all rights required by Ppci modules
 */
class Rights extends RightsPpci
{
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
        "admin" => ["admin"]
    ];

}