<?php
namespace Ppci\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Ppci\Libraries\SmartyParam;
use Psr\Log\LoggerInterface;
use Ppci\Libraries;

/**
 * Generic controller for prototypephp
 */
class PpciController extends \App\Controllers\BaseController
{
    protected $session;
    protected $message;
    protected $init;
    /**
     * 
     * Systematic code used
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     * @return void
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);

        $this->init = service("PpciInit");
        $this->init::Init();
        $this->session = session();
        /**
         * Add messages to user and syslog
         */
        $this->message = service('MessagePpci');
                        
    }

    protected function isAuthorized(bool $hasConnected = false, array $rights = [])
    {
        $ok = true;
        if (!$this->init::isDbversionOk) {
            $ok = false;
        } elseif ($hasConnected && !$this->session->isConnected) {
            $ok = false;
            $this->message->set(_("Vous devez vous connecter avant de pouvoir accéder au module demandé"), true);
        }

        $security = \Config\Services::security();
        if (!$this->request->is('post')) {
            $ok = false;
        }
        if (!$ok) {
            $this->message->set(_("L'exécution du module demandé n'est pas autorisé"), true);
            return redirect()->to(site_url())->withCookies();
        }
    }
}