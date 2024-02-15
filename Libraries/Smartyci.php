<?php
namespace Ppci\Libraries;

//require_once('../vendor/smarty/smarty/libs/Smarty.class.php');
use \Smarty\Smarty;

class Smartyci extends \Smarty
{
    protected $SMARTY_variables = array(
        "entete" => "entete.tpl",
        "enpied" => "enpied.tpl",
        "corps" => "main.tpl",
        "display" => "/display",
        "favicon" => "/favicon.png",
        "APPLI_titre" => array(),
        "LANG" => "fr"
    );
    protected $htmlVars = array(
        "menu",
        "LANG",
        "message",
        "texteNews",
        "doc",
        "phpinfo",
        "markdownContent"
    );
    protected $templateMain = "about_fr.tpl";
    public function __construct()
    {
        parent::__construct();
        //$config =& get_config();
        $this->caching = false;
        $this->setTemplateDir(APPPATH . '/Views/templates');
        $this->setCompileDir(WRITEPATH . '/templates_c');
        //$this->setConfigDir($config['application_dir'] . 'third_party/Smarty-3.1.8/configs');
        $this->setCacheDir('cache');
        foreach ($this->SMARTY_variables as $k => $v) {
            $this->assign($k, $v);
        }
    }


    function set($value, $variable)
    {
        $this->assign($variable, $value);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Vue::send()
     */
    function send()
    {
        $message = service('MessagePpci');
        /*
         * Encodage des donnees avant envoi vers le navigateur
         */
        foreach ($this->getTemplateVars() as $key => $value) {
            if (!in_array($key, $this->htmlVars)) {
                $this->assign($key, esc($value));
            }
        }

        /*
         * Recuperation des messages
         */
        $this->assign("message", $message->getAsHtml());
        /*
         * Declenchement de l'affichage
         */
        try {
            $this->display($this->templateMain);
        } catch (\Exception $e) {
            echo $e->getMessage();
            //\printr(_("Une erreur a été détectée lors de la création de l'écran. Si le problème persiste, contactez l'administrateur de l'application."));
            //global $message;
            //$message->setSyslog($e->getMessage());
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
    function encodehtml($data)
    {
        return esc($data);
    }
}