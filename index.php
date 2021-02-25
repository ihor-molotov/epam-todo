<?php
require_once 'core/init.php';
//$_SESSION['user_id'] = 1;
if(Input::exists()){
  if(Token::check('csrf_login', Input::get('csrf_login'), 'Delete')){
    //Validate Inputs
    $validate = new Validation();
    $validate->check($_POST, array(
      'username' => array(
        'min' => array(4, 'Username can\'nt be less than 4 letters'),
        'required' => array(true, 'You must enter a username'),
        'max' => array(20, 'Username can\'nt be more than 20 letters'),
        'regexp' => array('/^[A-Za-z0-9]+(?:[ _-][A-Za-z0-9]+)*$/', 'Username is only English alphabets, numbers and underscores')
      ),
      'password' => array(
        'min' => array(5, 'Password can\'nt be less than 5 letters'),
        'required' => array(true, 'You must enter a password'),
        'max' => array(20, 'Password can\'nt be more than 20 letters'),
      )
    ));
    if($validate->passed() == true){
      //check if username exists or not! and if yes then check password to log him in.. if not create him as a new user
      $query = DB::getInstance()->get('users', array('username', '=', Input::get('username')));
      if(!$query->error()){
        if($query->count()){
          if($query->first()->password === Hash::make(Input::get('password'), $query->first()->salt)){
            //give him his session
            Session::put('user_id', $query->first()->id);
            Redirect::re();
          }else{
            Session::flash('password', 'You have entered a wrong password or you had chosen a used username.');
          }
        }else{
          //sign him up
          $salt = Hash::salt();
          if(DB::getInstance()->insert('users', array(
            'username' => Input::get('username'),
            'password' => Hash::make(Input::get('password'), $salt),
            'salt' => $salt
          ))){
            //give him his session
            Session::put('user_id', DB::getInstance()->get('users', array('username', '=', Input::get('username')))->first()->id);
            Redirect::re();
          }
        }
      }
    }else{
      $err = $validate->errors();
    }
  }else{
    Redirect::re();
  }
}


?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ToDo List</title>

    <link href="css/style.css" rel="stylesheet"/>
    <meta id="csrf_token" content="<?php echo Token::generate('csrf_token'); ?>" />
  </head>
  <body>
    <script>
    function doAction(id, action){
      //send ajax request to mark as done
      var xml = new XMLHttpRequest(),
      formData = new FormData(),
      csrfToken = document.getElementById("csrf_token").getAttribute('content');
      formData.append('csrf', csrfToken);
      formData.append('msgId', id);
      formData.append('action', action);
      xml.onreadystatechange = function(){
        if(xml.readyState === 4 && xml.status === 200){
          if(xml.response === 'success'){
            //update items
            update();
          }else{
            location.reload();
          }
        }
      }
      xml.open('POST', 'Ajax.php');
      xml.send(formData);
    }

    function addNew(){
      //send ajax request
      var xml = new XMLHttpRequest(),
      formData = new FormData(),
      csrfToken = document.getElementById("csrf_token").getAttribute('content'),
      msgText = document.getElementById("addItem").value
      if(msgText !== "" && msgText.replace(/\s/g, "") !== ""){
        formData.append('csrf_token', csrfToken);
        formData.append('msgText', msgText);
        xml.onreadystatechange = function(){
          if(xml.readyState === 4 && xml.status === 200){
            if(xml.response === 'success'){
              //update items
              update();
              document.getElementById("addItem").value = "";
            }else{
              location.reload();
            }
          }
        }
        xml.open('POST', 'Ajax.php');
        xml.send(formData);
      }
    }
    </script>
    <div class="container">
      <div class="box">
        <h2 class="page-header">TO DO</h2>
        <?php if(Session::exists('user_id')): ?>
          <ul id="items">
          </ul>
          <div class="parent">
            <input type="text" id="addItem" placeholder="Type a new item here" />
            <input type="submit" value="Add" onclick="addNew();" />
              <a href="/logout.php">Logout</a>
          </div>
        <?php else: ?>
          <!-- LOGIN -->
          <a style="color: red;"><?php echo Session::flash('password'); ?></a>
          <div class='loginbox'>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" >
              <div class="login">
                <label for="username">Your Username: </label>
                <input type="text" name="username" id="username" />
                <a style="color: red;"><?php echo @$err['username']; ?></a>
              </div>
              <div class="login">
                <label for="password">Your Password: </label>
                <input type="password" name="password" id="password" />
                <a style="color: red;"><?php echo @$err['password']; ?></a>
                <a style="color: red;"><?php echo Session::flash('password'); ?></a>
              </div>
              <a>If you're a new user type a new password to register</a>

              <input type="hidden" name="csrf_login" value="<?php echo Token::generate('csrf_login'); ?>" />
              <input type="submit" value="Let me in" />
            </form>
          </div>
        <?php endif; ?>
      </div>
    </div>
    <!-- including Script files -->
    <script>
    function update(){
      //bt get el items
      if(document.getElementById("items")){//to avoid errors
        var xml = new XMLHttpRequest(),
        formData = new FormData(),
        items = document.getElementById("items"),
        csrfToken = document.getElementById("csrf_token").getAttribute('content');
        formData.append('csrftoken', csrfToken);
        formData.append('getMessage', '1');
        xml.onreadystatechange = function(){
          if(xml.readyState === 4 && xml.status === 200){
            //update items
            items.innerHTML = xml.response;
          }
        }
        xml.open('POST', 'Ajax.php');
        xml.send(formData);
      }
    }
    update();
    </script>
  </body>
</html>
