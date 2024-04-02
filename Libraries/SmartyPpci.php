<?php
namespace Ppci\Libraries;

use \Smarty\Smarty;
use \Ppci\Config\SmartyParam;


class SmartyPpci
{
    /**
     * Generic variables used when display templates
     *
     * @var array
     */
    protected $SMARTY_variables = array(
        "header" => "ppci/header.tpl",
        "footer" => "ppci/footer.tpl",
        "corps" => "main.tpl",
        "display" => "/display",
        "favicon" => "/favicon.png",
        "APPLI_title" => "Ppci",
        "APPLI_titre" => "Ppci",
        "LANG" => array(
            "date" => array(
                "locale" => "fr",
                "formatdate" => "DD/MM/YYYY",
                "formatdatetime" => "DD/MM/YYYY HH:mm:ss",
                "formatdatecourt" => "dd/mm/yy"
            )
        ),
        "menu" => "",
        "isConnected" => 0,
        "appliAssist" => "",
        "developpementMode" => 1,
        "messageError" => 0,
        "copyright" => "Copyright © 2024"
    );
    /**
     * Variables that must not encoded before send
     *
     * @var array
     */
    public $htmlVars = array(
        "menu",
        "LANG",
        "message",
        "texteNews",
        "doc",
        "phpinfo",
        "markdownContent"
    );
    public $templateMain;
    protected \Smarty $smarty;
    public function __construct()
    {
        if (!isset($this->smarty)) {
            $this->smarty = new \Smarty();
        }
        $smp = new SmartyParam();
        $this->smarty->caching = false;
        $this->smarty->setTemplateDir($smp->params["templateDir"]);
        $this->smarty->setCompileDir(ROOTPATH . $smp->params["compileDir"]);
        $this->templateMain = $smp->params["template_main"];
        $this->smarty->setCacheDir('cache');
        /**
         * Assign variables from app/Config/App
         */
        $appConfig = service("AppConfig");
        $this->SMARTY_variables["copyright"] = $appConfig->copyright;
        /**
         * Assign variables from dbparam table
         */
        $dbparam = service("Dbparam");
        $this->SMARTY_variables["APPLI_title"] = $dbparam->getParam("APPLI_title");
        /**
         * Assign all variables to Smarty class
         */
        foreach ($this->SMARTY_variables as $k => $v) {
            $this->smarty->assign($k, $v);
        }
    }
    /**
     * Add variable to Smarty
     *
     * @param [type] $value
     * @param string $variableName
     * @return void
     */
    function set($value, string $variableName)
    {
        $this->smarty->assign($variableName, $value);
    }
    /**
     * Trigger display
     *
     * @return void
     */
    function send()
    {
        $message = service('MessagePpci');
        if ($message->is_error) {
            $this->set(1, "messageError");
        }
        /**
         * Encode data before send
         */
        foreach ($this->smarty->getTemplateVars() as $key => $value) {
            if (!in_array($key, $this->htmlVars)) {
                $this->smarty->assign($key, esc($value));
            }
        }

        /**
         * Get messages
         */
        $this->smarty->assign("message", $message->getAsHtml());
        /**
         * Trigger the display
         */
        try {
            $this->smarty->display($this->templateMain);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Return the content of a variable
     *
     * @param string $variable
     * @return string|array
     */
    function get($variable = "")
    {
        return $this->smarty->getTemplateVars($variable);
    }
    /**
     * Legacy function
     * encode data to html display
     *
     * @param [type] $data
     * @return 
     */
    function encodehtml($data)
    {
        return esc($data);
    }
    function fetch(string $template)
    {
        return $this->smarty->fetch($template);
    }
}