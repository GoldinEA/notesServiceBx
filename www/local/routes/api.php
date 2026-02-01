<?php

use Bitrix\Main\Routing\RoutingConfigurator;
use Notes\Test\Controllers\ApiController;


return function (RoutingConfigurator $routes) {

    $routes->post('/api/v1/notes/add', [ApiController::class, 'add']);
    $routes->get('/api/v1/notes/list', [ApiController::class, 'list']);
    $routes->get('/api/v1/notes/get', [ApiController::class, 'get']);
    $routes->delete('/api/v1/notes/delete', [ApiController::class, 'delete']);
    $routes->patch('/api/v1/notes/update', [ApiController::class, 'update']);

};
