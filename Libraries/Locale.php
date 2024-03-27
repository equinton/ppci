<?php
namespace Ppci\Libraries;

class Locale
{


    public array $LANG = array(
        "locale" => "fr",
        "date" => [
            "locale" => "fr",
            "formatdate" => "DD/MM/YYYY",
            "formatdatetime" => "DD/MM/YYYY HH:mm:ss",
            "formatdatecourt" => "dd/mm/yy",
            "maskdatelong" => "d/m/Y H:i:s",
            "maskdate" => "d/m/Y",
            "maskdateexport" => 'd-m-Y'
        ]
    );

    function setLocale(string $locale)
    {
        if ($locale = "en") {
            $this->LANG["locale"] = "en";
            $this->LANG["date"]["locale"] = "en";
        } else if ($locale = "us") {
            $this->LANG["locale"] = "us";
            $this->LANG["date"] = [
                "locale" => "us",
                "formatdate" => "MM/DD/YYYY",
                "formatdatetime" => "MM/DD/YYYY HH:mm:ss",
                "formatdatecourt" => "mm/dd/yy",
                "maskdatelong" => "m/d/Y H:i:s",
                "maskdate" => "m/d/Y",
                "maskdateexport" => 'Y-m-d'
            ];
        }
        session()->set($this->LANG);
    }
}