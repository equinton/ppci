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
        "appliList" => ["admin"],
        "appliDisplay" => ["admin"],
        "appliChange" => ["admin"],
        "appliWrite" => ["admin"],
        "appliDelete" => ["admin"],
        "aclloginList" => ["admin"],
        "aclloginChange" => ["admin"],
        "aclloginWrite" => ["admin"],
        "aclloginDelete" => ["admin"],
        "groupList" => ["admin"],
        "groupChange" => ["admin"],
        "groupWrite" => ["admin"],
        "groupDelete" => ["admin"],
        "acoDisplay" => ["admin"],
        "acoChange" => ["admin"],
        "acoWrite" => ["admin"],
        "acoDelete" => ["admin"],
        "dbparamList" => ["admin"],
        "dbparamWriteGlobal" => ["admin"],
        "logList" => ["admin"],
        "requestList" => ["param"],
        "requestChange" => ["param"],
        "requestWrite" => ["param"],
        "requestDelete" => ["param"],
        "requestExec" => ["param"],
        "requestExecList" => ["param"],
        "requestWriteExec" => ["param"],
        "requestCopy" => ["param"],
        "backupDisplay" => ["admin"],
        "backupExec" => ["admin"],
        "backupSend" => ["admin"],
        "systemShowServer" => ["admin"],
        "systemShowSession" => ["admin"],
    ];

}