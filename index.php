<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/utils/Response.php';

// load routes
$routes = require_once __DIR__ . '/routes/api.php';

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

// remove base path
$path = parse_url($requestUri, PHP_URL_PATH);
$basePath = '/orizon';
if ($basePath && strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

// remove trailing slash
$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

// function to find route
function findRoute($routes, $method, $path) {
    foreach ($routes as $route => $handler) {
        list($routeMethod, $routePath) = explode(' ', $route, 2);
        
        if ($routeMethod !== $method) {
            continue;
        }
        
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        // Check if the path matches the pattern
        if (preg_match($pattern, $path, $matches)) {
            // Extract parameters
            $params = array_filter($matches, function($key) {
                return !is_numeric($key);
            }, ARRAY_FILTER_USE_KEY);
            
            return [
                'handler' => $handler,
                'params' => $params
            ];
        }
    }
    
    return null;
}

// Find route
$matchedRoute = findRoute($routes, $method, $path);

// If no route is found
if (!$matchedRoute) {
    // Check if the path exists
    $pathExists = false;
    foreach ($routes as $route => $handler) {
        list($routeMethod, $routePath) = explode(' ', $route, 2);
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        $pattern = '#^' . $pattern . '$#';
        
        if (preg_match($pattern, $path)) {
            $pathExists = true;
            break;
        }
    }
    
    if ($pathExists) {
        Response::methodNotAllowed();
    } else {
        Response::notFound('Endpoint not found');
    }
}

// Extract controller, action e params
$handler = $matchedRoute['handler'];
$params = $matchedRoute['params'];

$controllerName = $handler['controller'];
$actionName = $handler['action'];

// Load controller
$controllerPath = __DIR__ . '/controllers/' . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    Response::error('Controller not found', 500);
}

require_once $controllerPath;
// Create db connection
$database = new Database();
$db = $database->getConnection();

// Create controller
$controller = new $controllerName($db);

// Verify if action exists
if (!method_exists($controller, $actionName)) {
    Response::error('Action not found', 500);
}
try {
    $paramValues = array_values($params);
    call_user_func_array([$controller, $actionName], $paramValues);
} catch (Exception $e) {
    Response::error('Server internal error: ' . $e->getMessage(), 500);
}
