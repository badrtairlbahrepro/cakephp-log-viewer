<?php
/**
 * Routes configuration for LogViewer plugin
 */

declare(strict_types=1);

use Cake\Routing\RouteBuilder;

return function (RouteBuilder $builder) {
    $builder->scope('/logs', function (RouteBuilder $builder) {
        $builder->connect('/', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'index']);
        $builder->connect('/export/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'export']);
        $builder->connect('/view/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'view']);
        $builder->connect('/clear/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'clear']);
        $builder->connect('/download/*', ['plugin' => 'LogViewer', 'controller' => 'Logs', 'action' => 'download']);
    });
};

