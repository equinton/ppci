<?php
namespace Ppci\Controllers;

use Ppci\Libraries\LastRelease;
use Ppci\Libraries\Markdown;
use Ppci\Libraries\Submenu;

class Utils extends PpciController {

    function getLastRelease() {
        $lib = new LastRelease();
        return $lib->index();
    }

    function markdown(...$params) {
        $lib = new Markdown();
        return $lib->index($params);
    }

    function submenu(string $name) {
        $lib = new Submenu();
        return $lib->index($name);
    }
}