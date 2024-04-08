<?php
namespace Ppci\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $conf = new App();
        if ($conf["adminMustUseTotp"]) {
            $query = explode("/", uri_string());
            if (!empty($query)) {
                $moduleName = $query[0];
                if (!empty($query[1])) {
                    $moduleName .= ucfirst($query[1]);
                }
                $ppciRights = new \Ppci\Config\Rights();
                if ($ppciRights->isAdminRequired($moduleName)) {
                    
                }

            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}