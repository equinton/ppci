<?php

namespace Ppci\Libraries;

use Config\App;
use Ppci\Config\Ppci;
use Ppci\Models\PpciException;

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
        /**
         * @var App
         */
        $appConfig = service("AppConfig");
        if (!array_key_exists($locale, $appConfig->locales)) {
            $locale = array_key_first($appConfig->locales);
        }
        $_SESSION["date"] = $appConfig->locales[$locale];
        $_SESSION["locale"] = $locale;
        helper('cookie');
        set_cookie("locale", $locale, 31536000);

        /**
         * Parameters for gettext
         */
        if (!setlocale(LC_ALL, "C")) {
            throw new \Ppci\Libraries\PpciException("Locale not initialized");
        };
        bindtextdomain($locale, $appConfig->localePath);
        bind_textdomain_codeset($locale, "UTF-8");
        textdomain($locale);
    }

}
