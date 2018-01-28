<?php
  include('./classes/DB.php');
  include('./classes/Login.php');

  //checks if user is logged in
  if(!Login::isLoggedIn()){
    die('Not logged in');
  }
  //if the login button is pushed
  if(isset($_POST['confirm'])){
    //if the checkbox is checked
    if(isset($_POST['alldevices'])){
      //deletes the token from login_tokens
      DB::query('DELETE FROM login_tokens WHERE user_id=:user_id',array(':user_id'=>Login::isLoggedIn()));
    }else{
      //checks the cookie
      if(isset($_COOKIE['SNID'])){
        //deletes the cookie
        DB::query('DELETE FROM login_tokens WHERE token=:token',array(':token'=>sha1($_COOKIE['SNID'])));
      }
      //sets 2 cookies
      setcookie('SNID','1',time()-3600);
      setcookie('SNID_','1',time()-3600);

    }
  }

?>
<h1>Logout of account</h1>
<p>Are you sure?</p>
<form action="logout.php" method="post">
  <input type="checkbox" name="alldevices" value="alldevices"> Logout of all devices
  <input type="submit" name="confirm" value="confirm">
</form>
