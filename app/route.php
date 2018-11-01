<?php
class Route
{
    static function init()
    {
        $controllerName = 'Homepage';

        $routes = explode('/', $_SERVER['REQUEST_URI']);

        if (!empty($routes[1]) ) {
            $controllerName = $routes[1];
        }

        $controllerName = $controllerName .'Controller';

        $controllerFile = ucfirst($controllerName).'.php';
        $controllerPath = "../app/controller/".$controllerFile;

        if(file_exists($controllerPath)) {
            include "../app/controller/".$controllerFile;
        } else {
            echo 'Page not found!';die;
        }

        $controller = new $controllerName;
        $action = 'indexAction';

        if(method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo 'Page not found!';die;
        }
    }

}