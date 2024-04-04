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
        /**
         * Record the call into the log table
         */
        helper("ppci");
        setLogRequest($request);                
    }

}