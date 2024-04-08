<?php
namespace Ppci\Filters;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Redirect to route from legacy variables "module" or "moduleBase" + "action"
 */
class LegacyRouteFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        /**
         * Legacy routes
         */
        $session = session();
        $newroute = "";
        if (!empty ($_REQUEST["module"])) {
            $newroute = $_REQUEST["module"];
            unset($_REQUEST["module"]);
        } else if (!empty ($_REQUEST["moduleBase"]) && !empty ($_REQUEST["action"])) {
            $newroute = $_REQUEST["moduleBase"] . $_REQUEST["action"];
            unset($_REQUEST["moduleBase"]);
            unset($_REQUEST["action"]);
        }
        if (!empty ($newroute)) {
            if (!empty ($_POST)) {
                $session->setFlashData("POST", $_POST);
            }
            if (!empty($_GET)) {
                $session->setFlashData("GET", $_GET);
            }
            if (!empty($_REQUEST)) {
                $session->setFlashData("REQUEST", $_REQUEST);
            }
            return redirect($newroute)->withHeaders()->withInput()->withCookies();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}