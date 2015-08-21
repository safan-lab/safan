<?php

return array(
    // Error command 404
    '/^\/(404)?\/?$/i' => array(
        'type'       => 'RegExp',
        'module'     => 'SafanResponse',
        'controller' => 'error',
        'action'     => 'notfound',
        'matches'    => array(''),
    ),
    // Standard cli route
    '/^([A-Za-z-_]+)\:([A-Za-z-_]+)$/i' => array(
        'type'       => 'RegExp',
        'module'     => '',
        'controller' => 'index',
        'action'     => 'index',
        'matches'    => array('', 'module', 'controller', 'action'),
    ),
);