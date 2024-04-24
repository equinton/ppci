<?php
namespace Ppci\Controllers;
use Ppci\Libraries\About;
use Ppci\Libraries\News;
use Ppci\Libraries\Phpinfo;
use Ppci\Libraries\Structure;
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
    function structureHtml() {
        $structure = new Structure();
        return $structure->html();
    }
    function structureLatex() {
        try {
        $structure = new Structure();
        return $structure->latex();
        }catch (\Exception $e) {
            return "default";
        }
    }
    function structureSchema() {
        try {
            $structure = new Structure();
        return $structure->schema();
        } catch (\Exception $e) {
            return "default";
        }
        
    }
    function test() {

    }
}