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
        "entete" => "entete.tpl",
        "enpied" => "enpied.tpl",
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
        "messageError" => 0
    );
    /**
     * Variables that must not encoded before send
     *
     * @var array
     */
    protected $htmlVars = array(
        "menu",
        "LANG",
        "message",
        "texteNews",
        "doc",
        "phpinfo",
        "markdownContent"
    );
    //protected $templateMain = "about_fr.tpl";
    public $templateMain = "main.htm";
    protected \Smarty $smarty;
    public function __construct()
    {
        if (!isset ($this->smarty)) {
            $this->smarty = new \Smarty();
        }
        new SmartyParam();
        $this->smarty->caching = false;
        $this->smarty->setTemplateDir(SmartyParam::$templateDir);
        $this->smarty->setCompileDir(ROOTPATH . SmartyParam::$compileDir);
        //$this->setConfigDir($config['application_dir'] . 'third_party/Smarty-3.1.8/configs');
        $this->smarty->setCacheDir('cache');
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
            $this->SMARTY_variables["messageError"] = 1;
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
}