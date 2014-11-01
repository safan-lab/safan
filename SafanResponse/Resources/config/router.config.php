<?php

return array(
    // Welcome page
    '/^\/$/i' => array(
        'type' => 'RegExp',
        'module' => 'SafanResponse',
        'controller' => 'welcome',
        'action' => 'index',
        'matches' => array('', 'module', 'controller', 'action'),
    ),
    // Error page 404
    '/^\/(404)?\/?$/i' => array(
        'type' => 'RegExp',
        'module' => 'SafanResponse',
        'controller' => 'error',
        'action' => 'error404',
        'matches' => array(''),
    ),
    // Standard MVC route
    '/^\/([A-Za-z0-9_-]+)\/?([A-Za-z0-9_-]+)?\/?([A-Za-z0-9_-]+)?\/?$/i' => array(
        'type' => 'RegExp',
        'module' => '',
        'controller' => 'index',
        'action' => 'index',
        'matches' => array('', 'module', 'controller', 'action'),
    ),
);