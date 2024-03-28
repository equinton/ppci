<?php
namespace Ppci\Libraries;

use Ppci\Config\Ppci;
use Ppci\Models\Ppciexception;

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
        $localeGettext = "fr_FR";
        if ($locale = "en") {
            $this->LANG["locale"] = "en";
            $this->LANG["date"]["locale"] = "en";
            $localeGettext = "en_GB";
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
            $localeGettext = "en_US";
        }
        session()->set($this->LANG);
        /**
         * Parameters for gettext
         */
        $param = new Ppci();
        if (!setlocale(LC_ALL, "C")) {
            throw new Ppciexception("Locale not initialized");
        }
        ;
        bindtextdomain($locale, $param->localePath);
        bind_textdomain_codeset($locale, "UTF-8");
        textdomain($locale);
    }
}