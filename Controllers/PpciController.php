<?php
namespace Ppci\Controllers;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Ppci\Libraries;

/**
 * Generic controller for prototypephp
 */
class PpciController extends \App\Controllers\BaseController
{
    protected $session;
    protected $message;
    /**
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
        /**
         * Start the session
         */
        $this->session = session();
        /**
         * Add generic functions
         */
        helper('ppci');
        /**
         * Add messages to user and syslog
         */
        $this->message = service('MessagePpci');
        /**
         * Set default parameters
         */
        $this->session->set(
            array(
                "APPLI_code" => "Ppci"
            )
        );

    }
}