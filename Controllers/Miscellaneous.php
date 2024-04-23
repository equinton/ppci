<?php
namespace Ppci\Controllers;
use Ppci\Libraries\Phpinfo;
class Miscellaneous extends PpciController {
    function about() {

    }
    function phpinfo() {
        $phpinfo = new Phpinfo();
        return $phpinfo->getPhpinfo();
    }
    function news() {

    }
    function test() {

    }
}