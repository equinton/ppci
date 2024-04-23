<?php
namespace Ppci\Controllers;

class Submenu extends PpciController
{
    private $submenu;

    function __construct()
    {
        $this->submenu = new \Ppci\Libraries\Submenu();
    }
    function administration()
    {
        return $this->submenu->generateSubmenu("administration");
    }
    function index(string $name) {
        return $this->submenu->generateSubmenu($name);
    }
}