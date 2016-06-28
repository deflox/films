<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';

/*
 * Config
 */
$config = new \Noodlehaus\Config(__DIR__ . '/../config/default.php');

/*
 * Slim App
 */
$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => $config->get('db.host'),
            'database' => $config->get('db.database'),
            'username' => $config->get('db.user'),
            'password' => $config->get('db.password'),
            'charset' => $config->get('db.charset'),
            'collation' => $config->get('db.collation'),
            'port' => $config->get('db.port'),
        ],
    ],
]);

$container = $app->getContainer();

/*
 * Eloquent
 */
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function($c) use ($capsule) {
    return $capsule;
};

/*
 * Dependencies
 */
$container['view'] = function($c) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../resources/views');

    $view->addExtension(new \Slim\Views\TwigExtension(
        $c->router,
        $c->request->getUri()
    ));

    $view->getEnvironment()->addGlobal('session', $_SESSION);

    return $view;
};

/*
 * Local libraries
 */
$container['auth'] = function($c) {
    return new App\Libraries\Auth($c);
};
$container['validator'] = function($c) {
    return new \App\Libraries\Validator($c);
};

/*
 * Controllers
 */
$container['AuthController'] = function($c) {
    return new App\Controllers\AuthController($c);
};
$container['FilmController'] = function($c) {
    return new App\Controllers\FilmController($c);
};
$container['StatisticsController'] = function($c) {
    return new App\Controllers\StatisticsController($c);
};

/*
 * Middleware
 */
$app->add(new \RKA\Middleware\IpAddress());

/*
 * Routes
 */
require __DIR__ . '/routes.php';