<?php
  //include database controller
  include('classes/DB.php');
  //if login button is pressed
  include('classes/Login.php');
  if(!Login::isLoggedIn()){
    if(isset($_POST['login'])){
      $username = $_POST['username'];
      $password = $_POST['password'];
      //check if username exists
      if(DB::query('SELECT username FROM users WHERE username=:username',array('username'=>$username))){
        //check if password matches
        if(password_verify($password,DB::query('SELECT password FROM users WHERE username=:username',array(':username'=>$username))[0]['password'])){
          echo 'Logged in';
          //for encryption
          $cstrong=True;
          //creates a random token
          $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
          //gets user_id from datbase since it is a foreign key
          $user_id = DB::query('SELECT id FROM users WHERE username=:username',array(':username'=>$username))[0]['id'];
          //stores token in database
          DB::query('INSERT INTO login_tokens (token,user_id) VALUES (:token,:user_id)',array(':token'=>sha1($token),':user_id'=>$user_id));
          //creates a cookie
          setcookie("SNID",$token,time()+60*60*24*7,'/',NULL,NULL,TRUE);
          //creates second cookie
          setcookie("SNID_",'1',time()+60*60*24*7,'/',NULL,NULL,TRUE);
        }else{
          echo 'Invalid username or password';
        }
      }else{
        echo 'Invalid username or password';
      }

    }
  }else{
    echo 'Already logged in';
  }

?>

<?php
if(!Login::isLoggedIn()){
  echo '<h1>Login to your account</h1>
  <form action="login.php" method="post">
    <input type="text" name="username" value="" placeholder="username"><p></p>
    <input type="password" name="password" value="" placeholder="password"><p></p>
    <input type="submit" name="login" value="login">
  </form>';
}?>
