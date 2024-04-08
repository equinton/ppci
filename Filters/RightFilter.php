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
        //echo $_SERVER['QUERY_STRING'] . "<br>";
        $query = explode("/", uri_string());
        if (!empty($query)) {
            $moduleName = $query[0];
            if (!empty($query[1])) {
                $moduleName .= ucfirst($query[1]);
            }
            $appRights = new \App\Config\Rights();
            $requiredRights = $appRights->getRights($moduleName);
            if (empty($requiredRights)) {
                $ppciRights = new \Ppci\Config\Rights();
                $requiredRights = $ppciRights->getRights($moduleName);
            }
            if (!empty($requiredRights)) {


                $session = \Config\Services::session();
                if (!isset($_SESSION["isLogged"])) {
                    $login = new \Ppci\Libraries\Login();
                    $retour = $login->getLogin();
                    if (isset($retour)) {
                        return redirect()->to(site_url($retour));
                    }
                }

                $userRights = $session->get("userRights");
                if (is_null($userRights)) {
                    $userRights = [];
                }
                $testRights = array_intersect($requiredRights, $userRights);
                if (count($testRights) == 0) {
                    $message = service('MessagePpci');
                    $message->set(_("Vous ne disposez pas des droits nécessaires pour exécuter cette fonction"), true);
                    helper("ppci");
                    setLogRequest($request, "ko: insufficient rights");
                    $defaultPage = new \Ppci\Libraries\DefaultPage();
                    return ($defaultPage->display());
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
