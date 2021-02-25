<?php

session_start();
$GLOBALS['config'] = array(
    'db' => array(
        'host' => '',
        'name' => '',
        'user' => '',
        'pass' => ''
    )
);


spl_autoload_register(
    function ($class) {
        require_once "classes/{$class}.php";
    }
);
