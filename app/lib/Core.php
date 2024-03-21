<?php

class Core
{
    protected $controller;
    protected $method;
    protected $parameters = [];

    public function __construct()
    {
        $url = $this->getUrl();
        $this->controller = new Controller;

        if (empty($url) or ($url[0] == 'inicio' and count($url) == 1)) {
            $this->method = 'indexPage';
        } else {
            $controllerName = ucwords($url[0] ?? '') . 'Controller';
            $controllerFile = '../app/controllers/' . $controllerName . '.php';
            $methodName = $url[1] ?? 'inicio';

            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $this->controller = new $controllerName;

                if (method_exists($this->controller, $methodName)) {
                    $this->method = $methodName;
                    $this->parameters = array_slice($url, 2);
                } else {
                    $this->method = 'notFound';
                }
            } else {
                $this->method = 'notFound';
            }
        }

        call_user_func_array([$this->controller, $this->method], $this->parameters);
    }

    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        } else {
            return [];
        }
    }
}