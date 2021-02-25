<?php

class Session{

  public static function exists($name){
    if(isset($_SESSION[$name])){
      return true;
    }
    return false;
  }

  public static function put($name, $value){
    return $_SESSION[$name] = $value;
  }

  public static function get($name){
    return (isset($_SESSION[$name])) ? $_SESSION[$name] : false;
  }

  public static function delete($name){
    if(self::exists($name)){
      unset($_SESSION[$name]);
    }
  }

  public static function flash($name, $string = ''){
    if(self::exists($name)){
      $value = self::get($name);
      self::delete($name);
      return $value;
    }else{
      self::put($name, $string);
    }
  }

}
