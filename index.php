<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'Requests/Requests.php';

$request = new Request;

$request->url_elements = array();


if (isset($_SERVER['PATH_INFO'])) {
    $request->url_elements = explode('/', $_SERVER['PATH_INFO']);
}

$request->verb = $_SERVER['REQUEST_METHOD'];

 
switch ($request->verb) {
case 'GET':
    // code...
    if(isset($_GET))
    {
        $request->parameters = $_GET;
    }
    else
    {
        $request->parameters = NULL;
    }
    break;
case 'POST':
case 'PUT':
    $request->parameters = json_decode(file_get_contents('php://input'), 1);
    break; 
case 'DELETE':
default:
    // code...
    $request->parameters = array();
    
}

if($request->url_elements) {
    
    $controller_name = ucfirst($request->url_elements[1]).'Controller';
            
    function __autoload($controller_name)
    {
            
        if(!@(include 'Controllers/'.$controller_name.'.php')) {
            return false;
        }

        if (!@class_exists($controller_name, false)) {
            
               trigger_error("Unable to load class: $controller_name", E_USER_WARNING);
            
        }
            
    }    

    if(class_exists($controller_name)) {
            
        $controller = new $controller_name;
            
        $action_name = ucfirst($request->verb).'Action';
            
        $response = $controller->$action_name($request);

        echo $response;

        exit;
    }
    else
    {
        header('HTTP/1.0 400 Bad Request', true, 400);
        $response = 'Unknown Request For'.$request->url_elements[1];
        echo $response;
        exit;
    }
        
}
else
{
    header('HTTP/1.0 400 Bad Request', true, 400);
    $response = 'Unknown Request';
    exit;
            
}



