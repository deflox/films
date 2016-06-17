<?php

$app->get('/', 'FilmController:getIndex')->setName('home');

$app->post('/api/login', 'AuthController:postLogin')->setName('api.login');
$app->get('/api/logout', 'AuthController:getLogout')->setName('api.logout');
$app->post('/api/store/item', 'FilmController:storeItem')->setName('api.addItem');
