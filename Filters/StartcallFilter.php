<?php
namespace Ppci\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class StartcallFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null) {
        $init = service("PpciInit");
        $init::init();
        setLogRequest($request);
        /**
         * Uncode html vars
         */
        $_REQUEST = htmlDecode($_REQUEST);
    }
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
