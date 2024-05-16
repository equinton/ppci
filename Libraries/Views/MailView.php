<?php
namespace Ppci\Libraries\views;
use \Ppci\Config\SmartyParam;
/**
 * @author Eric Quinton
 * @copyright Copyright (c) 2015, IRSTEA / Eric Quinton
 * @license http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html LICENCE DE LOGICIEL LIBRE CeCILL-C
 *  Creation 30 sept. 2015
 */

class Mail
{
    private $param = array(
        "replyTo" => "",
        "subject" => "subject",
        "contents" => "text message",
        "mailTemplate" => "ppci/mail/mail.tpl" /* name of the main Smarty template used to send mails */
    );

    private \Smarty $smarty;
    /**
     * @var App
     */
    private $paramApp;

    /**
     * Constructeur de la classe, avec passage des parametres
     *
     * @param array $param
     */
    function __construct(array $param = array())
    {
        $this->setParam($param);
        if (!isset($this->param["from"])) {
            $this->paramApp = service("AppConfig");
            $this->param["from"] = $this->paramApp->APP_mail;
        }
    }

    /**
     * Assign the parameters
     *
     * @param array $param
     */
    function setParam(array $param)
    {
        foreach ($param as $key => $value) {
            if (isset($this->param[$key])) {
                $this->param[$key] = $value;
            }
        }
    }

    /**
     * Send mail with smarty template
     *
     * @param string $dest Mail of the recipient
     * @param string $subject subject of the mail
     * @param string $template_name name of the smarty template
     * @param array $data list of variables to transfert to the smarty instance
     * @param string $locale language used by the recipient
     * @param boolean $debug if true, display the content of the mail and the variables nor send message
     * @return bool
     */
    function SendMailSmarty( string $dest, string $subject, string $template_name, array $data, string $locale = "fr")
    {
        if (!isset($this->smarty)) {
            $this->smarty = new \Smarty();
            //new SmartyParam();
        $this->smarty->caching = false;
        $this->smarty->setTemplateDir(SmartyParam::$params["templateDir"]);
        $this->smarty->setCompileDir(ROOTPATH . SmartyParam::$params["compileDir"]);
        }
        $currentLocale = $_SESSION['LANG']["locale"];
        if ($locale != $currentLocale) {
            $localeClass = service("Locale");
            $localeClass->initGettext($locale);
        }
        foreach ($data as $variable => $value) {
            $this->smarty->set($variable, $value);
        }
        $this->smarty->assign("mailContent", $template_name);
        /**
         * Add the logo to the main template
         */
        $this->smarty->assign("logo", "data:image/png;base64," . chunk_split(base64_encode(file_get_contents(FCPATH."favicon.png"))));
        if (!$this->paramApp["MAIL_param"]["mailDebug"]) {
            $status = mail($dest, $subject, $this->smarty->fetch($this->param["mailTemplate"]), $this->getHeaders());
        } else {
            printA($this->param);
            printA($data);
            $this->smarty->display($this->param["mailTemplate"]);
            $status = true;
        }
        if ($locale != $currentLocale) {
            initGettext($currentLocale);
        }
        /**
         * Generate logs
         */
        $log = service("Log");
        empty($_SESSION["login"]) ? $login = "system" : $login = $_SESSION["login"];
        $log->setLog($login, "sendMail", "$dest / $subject");
        return $status;
    }

    /**
     * Get the headers
     *
     * @return string
     */
    function getHeaders()
    {
        return 'Content-type: text/html; charset=UTF-8;';
    }
}