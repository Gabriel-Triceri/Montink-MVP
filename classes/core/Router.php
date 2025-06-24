<?php
class Router {
    public static function route($url, $db) {
        $parts = explode('/', trim($url, '/'));
        $controller = !empty($parts[0]) ? $parts[0] : 'produto';
        $action = $parts[1] ?? 'index';
        $param = $parts[2] ?? null;
        $controllerName = ucfirst($controller) . 'Controller';
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';

        if (file_exists($controllerFile)) {
            require_once $controllerFile;

            $ctrl = new $controllerName($db);

            if (method_exists($ctrl, $action)) {
                if ($param !== null) {
                    $ctrl->$action($param); 
                } else {
                    $ctrl->$action();       
                }
            } else {
                echo "Ação '$action' não encontrada no controller '$controllerName'.";
            }
        } else {
            echo "Controller '$controllerName' não encontrado.";
        }
    }
}
