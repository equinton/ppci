<?php
namespace Ppci\Controllers;
use Ppci\Libraries\About;
use Ppci\Libraries\News;
use Ppci\Libraries\Phpinfo;
use Ppci\Libraries\System;
class Miscellaneous extends PpciController {
    function about() {
        $about = new About();
        return $about->index();
    }
    function phpinfo() {
        return Phpinfo::getPhpinfo();
    }
    function news() {
        return News::getNews();
    }
    function systemServer() {
        return System::index($_SERVER);
    }
    function systemSession() {
        return System::index($_SESSION);
    }

    function test() {

    }
}