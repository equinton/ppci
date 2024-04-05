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
            return redirect($newroute)->withCookies();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}