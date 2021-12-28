<?php
namespace app;
use libs;
require_once '../public/web.php';
class Web
{
    protected $route;
    protected $action;
    protected $params;
    public function __construct()
    {
        $router = new \libs\Router();
        $this->route = $router->getRoute();

        $controller = new $this->route['controller'];
        $this->action = $this->route['action'];
        $this->params = $this->route['params'] ?? [];
        call_user_func_array([$controller, $this->action], $this->params);
        echo '<pre>';
        var_dump($this->route);
        echo '</pre>';
    }
}