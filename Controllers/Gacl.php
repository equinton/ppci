<?php
namespace Ppci\Controllers;

class Gacl extends PpciController {

    function appliindex() {
        $lib = new \Ppci\Libraries\Aclappli();
        return $lib->list();
    }
    function applidisplay() {
        $lib = new \Ppci\Libraries\Aclappli();
        return $lib->display();
    }
    function applichange () {
        $lib = new \Ppci\Libraries\Aclappli();
        return $lib->change();
    }
    function appliwrite() {
        $lib = new \Ppci\Libraries\Aclappli();
        return $lib->write();
    }
    function applidelete() {
        $lib = new \Ppci\Libraries\Aclappli();
        return $lib->delete();
    }
    function loginindex() {

    }
    function loginchange() {

    }
    function loginwrite() {

    }
    function logindelete() {

    }
    function groupindex() {

    }
    function groupchange() {

    }
    function groupwrite() {

    }
    function groupdelete() {

    }
    function acodisplay() {
        $lib = new \Ppci\Libraries\Aclaco();
        return $lib->display();
    }
    function acochange() {
        $lib = new \Ppci\Libraries\Aclaco();
        return $lib->change();
    }
    function acowrite() {
        $lib = new \Ppci\Libraries\Aclaco();
        return $lib->write();
    }
    function acodelete () {
        $lib = new \Ppci\Libraries\Aclaco();
        return $lib->delete();
    }
}