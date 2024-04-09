<?php
namespace Ppci\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\App;
use Ppci\Libraries\Totp;
use Ppci\Models\Acllogin;

class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $conf = new App();
        if ($conf["adminMustUseTotp"]) {
            $adminOk = false;
            $query = explode("/", uri_string());
            if (!empty($query)) {
                $moduleName = $query[0];
                if (!empty($query[1])) {
                    $moduleName .= ucfirst($query[1]);
                }
                $ppciRights = new \Ppci\Config\Rights();

                if ($ppciRights->isAdminRequired($moduleName)) {
                    $app = service("AppConfig");
                    if (in_array("admin", $_SESSION["userRights"])) {
                        if (
                            isset($_SESSION["adminSessionTime"])
                            && ($_SESSION["adminSessionTime"] + $app->adminSessionDuration) < time()
                        ) {
                            $adminOk = true;
                            $_SESSION["adminSessionTime"] = time();
                        } else {
                            $aclLogin = new Acllogin();
                            $totp = new Totp();
                            $vue = service("Smarty");
                            if ($aclLogin->isTotp()) {
                                $vue->set(1, "isAdmin");
                                return $totp->input();
                            } else {
                                $message = service("MessagePpci");
                                $message->set(_("Vous devez activer la double identification TOTP pour accéder aux modules d'administration"), true);
                                return $totp->create();
                            }
                        }
                    }
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}