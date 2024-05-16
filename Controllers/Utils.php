<?php
namespace Ppci\Controllers;

use Ppci\Libraries\LastRelease;

class Utils extends PpciController {

    function getLastRelease() {
        $lib = new LastRelease();
        return $lib->index();
    }
}