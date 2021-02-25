<?php
$init = require_once 'core/init.php';
Session::delete('user_id');
Redirect::to('index.php');