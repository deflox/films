<?php

$app->get('/', 'FilmController:getIndex')->setName('home');

$app->post('/api/login', 'AuthController:postLogin')->setName('api.login');
$app->get('/api/logout', 'AuthController:getLogout')->setName('api.logout');

$app->post('/api/add/item', 'FilmController:addItem')->setName('api.add.item');
$app->post('/api/edit/item', 'FilmController:editItem')->setName('api.edit.item');
$app->post('/api/delete/item', 'FilmController:deleteItem')->setName('api.delete.item');
$app->post('/api/get/item', 'FilmController:getSingleItem')->setName('api.get.item');