<?php
session_start();
$GLOBALS['config'] = array(
  'db' => array(
    'host' => 'localhost',
    'name' => 'epam-todo',
    'user' => 'root',
    'pass' => 'root'
  )
);


spl_autoload_register(function($class){
  require_once "classes/{$class}.php";
});
