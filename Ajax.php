<?php
require_once 'core/init.php';

if(isset($_POST['msgId']) && isset($_POST['csrf']) && isset($_POST['action'])){
  $id     = htmlspecialchars($_POST['msgId']);
  $csrf   = htmlspecialchars($_POST['csrf']);
  $action = htmlspecialchars($_POST['action']);
  if(
    $id     !== '' && str_replace(' ', '', $id)     !== '' &&
    $action !== '' && str_replace(' ', '', $action) !== '' &&
    Token::check('csrf_token', $csrf)
  ){
    //check username if the same owner of the message
    $query = DB::getInstance()->get('items', array('id', '=', $id));
    if(!$query->error()){
      $res = $query->result();
      if(intval($res[0]->user) === intval(Session::get('user_id'))){ // check if the user id in session is the same owner of message
        if($action === 'mark_done'){
          //now execute the action
          if(DB::getInstance()->update('items', array('id', $id), array(
            'done' => 1
          ))){
            echo 'success';
          }
        }elseif($action === 'delete'){
          DB::getInstance()->delete('items', array('id', '=', $id));
          echo 'success';
        }
      }else{
        die('You\'re not allowed to do this action');
      }
    }else{
      die('Error fetching database');
    }
  }else{
    die('You didn\'nt provide a valid data');
  }
}

if(isset($_POST['csrf_token']) && isset($_POST['msgText'])){
  $csrf    = htmlspecialchars($_POST['csrf_token']);
  $msgText = htmlspecialchars($_POST['msgText']);
  if(
    $msgText !== '' && str_replace(' ', '', $msgText) !== '' &&
    $csrf    !== '' && str_replace(' ', '', $csrf)    !== '' &&
    Token::check('csrf_token', $csrf)
  ){
    //now execute the action
    DB::getInstance()->insert('items', array(
      'name' => $msgText,
      'user' => Session::get('user_id'),
      'created' => date('Y-m-d h:i:s')
    ));
    echo 'success';
  }else{
    die('You didn\'nt provide a valid data');
  }
}

if(isset($_POST['csrftoken']) && isset($_POST['getMessage'])){
  if(Token::check('csrf_token', htmlspecialchars($_POST['csrftoken'])) && $_POST['getMessage'] === '1'){
    $html = "";
    $query = DB::getInstance()->get('items', array('user', '=', Session::get('user_id')));
    if(!$query->error()){
      $result = $query->result();
    }else{
      die('ERROR while fetching data from database. please contact the adminstrator');
    }
    if(!empty($result)){
      foreach($result as $item){
        $done = ($item->done) ? ' done' : '' ;
        $id = $item->id;
        $date = $item->created;
        $text = $item->name;
        $mark = (!$item->done) ? '<span onclick="doAction(' . $item->id . ', \'mark_done\');">Mark as done</span>' : '' ;
        $html .= "<li class='item {$done}' data-id='{$id}' title='Created on: {$date}'><a>{$text}</a>{$mark}<span onclick=\"doAction({$id}, 'delete');\">Delete</span></li>";
      }
      echo $html;
    }else{
      echo "<p style='font-weight: bold; text-align: center; background: #EEE; padding: 10px 0;'>You didn't add any items yet</p>";
    }
  }else{
    die('You didn\'nt provide a valid data');
  }
}
