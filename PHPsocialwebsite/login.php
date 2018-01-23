<?php
  //include database controller
  include('classes/DB.php');
  //if login button is pressed
  if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    //check if username exists
    if(DB::query('SELECT username FROM users WHERE username=:username',array('username'=>$username))){
      //check if password matches
      if(password_verify($password,DB::query('SELECT password FROM users WHERE username=:username',array(':username'=>$username))[0]['password'])){
        echo 'Logged in';
      }else{
        echo 'Invalid username or password';
      }
    }else{
      echo 'Invalid username or password';
    }

  }
?>


<h1>Login to your account</h1>
<form action="login.php" method="post">
  <input type="text" name="username" value="" placeholder="username"><p></p>
  <input type="password" name="password" value="" placeholder="password"><p></p>
  <input type="submit" name="login" value="login">
</form>
