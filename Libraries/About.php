<?php
namespace Ppci\Libraries;

class About extends PpciLibrary
{
    function index()
    {
        $vue = service("Smarty");
        $vue->set($this->appConfig->version, "version");
        printA(($this->appConfig->versionDate));
        $vue->set($this->appConfig->versionDate, "versiondate");
        $locale = $_SESSION["FORMATDATE"];
        $search = APPPATH."Views/templates/about_" . $locale . ".tpl";
        if (file_exists($search)) {
            $filename = "about_" . $locale . ".tpl";
        } else {
            $filename = "about_en.tpl";
        }
        $vue->set($filename, "corps");
        return $vue->send();

    }
}