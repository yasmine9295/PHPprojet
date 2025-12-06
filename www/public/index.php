<?php

namespace App;
var_dump(file_exists(__DIR__ . '/../vendor/autoload.php'));


session_start(); /* */


spl_autoload_register(function ($class){
    $class = str_ireplace(["\\", "App"], ["/", ".."],$class);
    if(file_exists($class.".php")){
        include $class.".php";
    }
});


$requestUri = strtok($_SERVER["REQUEST_URI"], "?");
if(strlen($requestUri)>1)
    $requestUri = rtrim($requestUri, "/");
$requestUri = strtolower($requestUri);

$routes = yaml_parse_file("../routes.yml");


//Vérifier que l'uri existe dans les routes

if(isset($routes[$requestUri])){
    if(empty($routes[$requestUri])){
        die("Aucune route pour cette uri : page 404");
    }

    if(empty($routes[$requestUri]["controller"]) || empty($routes[$requestUri]["action"]) ){
        die("Aucun controller ou action pour cette uri : page 404");
    }

    $controller = $routes[$requestUri]["controller"];
    $action = $routes[$requestUri]["action"];

    if(!file_exists("../Controllers/".$controller.".php")){
        die("Aucun fichier controller pour cette uri");
    }

    include "../Controllers/".$controller.".php";

    $controller = "App\\Controllers\\".$controller;
    if(!class_exists($controller)){
        die("La classe du controller n'existe pas");
    }

    $objetController = new $controller();

    if(!method_exists($objetController, $action)){
        die("La methode du controller n'existe pas");
    }

    $objetController->$action();
    
} else {
    // Si aucune route ne correspond, vérifier si c'est une page dynamique
    $slug = ltrim($requestUri, '/');
    
    if(empty($slug)){
        // Rediriger vers la home si vide
        header("Location: /");
        exit;
    }
    
    // Tenter de charger une page dynamique
    include "../Controllers/Base.php";
    $controller = new \App\Controllers\Base();
    
    if(method_exists($controller, 'dynamicPage')){
        $controller->dynamicPage($slug);
    } else {
        // http_response_code(404);
        die("Aucune page trouvé: 404");
    }
}