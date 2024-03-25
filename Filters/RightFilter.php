<?php
namespace Ppci\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Test if the user has sufficient rights to execute the module
 */
class RightFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Do something here
        echo base_url(uri_string()) . "<br>";
        echo $_SERVER['QUERY_STRING'] . "<br>";
        $query = explode("/", $_SERVER['QUERY_STRING']);
        if (!empty ($query)) {
            $moduleName = $query[0];
            if (!empty ($query[1])) {
                $moduleName .= ucfirst($query[1]);
            }
            $appRights = new \App\Config\Rights();
            $requiredRights = $appRights->getRights($moduleName);
            if (empty ($requiredRights)) {
                $ppciRights = new \Ppci\Config\Rights();
                $requiredRights = $ppciRights->getRights($moduleName);
            }
            if (!empty ($requiredRights)) {
                $session = \Config\Services::session();
                $testRights = array_intersect($requiredRights, $session->get("userRights"));
                if (count($testRights) == 0) {
                    $message = service('MessagePpci');
                    $message->set(_("Vous ne disposez pas des droits nécessaires pour exécuter cette fonction"), true);
                    return redirect()->to(site_url());
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
