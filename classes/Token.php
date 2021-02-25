<?php

class Token{

  public static function generate($name){
    return Session::put($name, md5(uniqid()));
  }

  public static function check($name, $value, $hook = ''){
    $val  = Session::get($name);
    if($value === $val){
      if($hook === 'Delete'){
        Session::delete($name);
      }
      return true;
    }
    return false;
  }

}
