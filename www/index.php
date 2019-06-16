<?php

require 'conf.inc.php';

function myAutoloader($class)
{
    $classPath = 'core/'.$class.'.class.php';
    $classModel = 'models/'.$class.'.class.php';
    if (file_exists($classPath)) {
        include $classPath;
    } elseif (file_exists($classModel)) {
        include $classModel;
    }
}

// La fonction myAutoloader est lancé sur la classe appelée n'est pas trouvée
spl_autoload_register('myAutoloader');

// Récupération des paramètres dans l'url - Routing
$slug = explode('?', $_SERVER['REQUEST_URI'])[0];
$routes = Routing::getRoute($slug);
extract($routes);

$container = [
    Users::class => function ($container) {
        return new Users();
    },
    UsersController::class => function ($container) {
        $usersController = $container[Users::class]($container);

        return new UsersController($usersController);
    },
    PagesController::class => function ($container) {
        return new PagesController();
    },
];

// Vérifie l'existence du fichier et de la classe pour charger le controlleur
if (file_exists($cPath)) {
    include $cPath;
    if (class_exists($c)) {
        //instancier dynamiquement le controller
        $cObject = $container[$c]($container);
        //vérifier que la méthode (l'action) existe
        if (method_exists($cObject, $a)) {
            //appel dynamique de la méthode
            $cObject->$a();
        } else {
            die('La methode '.$a." n'existe pas");
        }
    } else {
        die('La class controller '.$c." n'existe pas");
    }
} else {
    die('Le fichier controller '.$c." n'existe pas");
}
