<?php

class Config{
  public static function get($path){
    if($path){
      $config = $GLOBALS['config'];
      $path = explode('/', $path);
      foreach($path as $bit){
        if(isset($config[$bit])){
          $config = $config[$bit];
        }
      }
      if($config !== $GLOBALS['config']){
        return $config;
      }
    }
    return false;
  }
}
