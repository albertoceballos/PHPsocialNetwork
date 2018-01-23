<?php
//includes database controller class
  include('classes/DB.php');

//if post button is pushed then get the information
  if(isset($_POST['createaccount'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    //checks if username already exists in the database
    if(!DB::query('SELECT username FROM users WHERE username=:username',array('username'=>$username))){

      //checks that the username isn't longer than the allowed length
      if(strlen($username)>=3 && strlen($username)<=32){
        //checks that the user has valid keys
        if(preg_match('/[a-zA-z0-9_]+/',$username)){
          //checks if password is valid
          if(strlen($password) >=6 && strlen($password)<=60){
            //checks if the email is valid
            if(filter_var($email,FILTER_VALIDATE_EMAIL)){
              //runs query to database controller and hashes password
              DB::query('INSERT INTO users (username,password,email) VALUES (:username,:password,:email)',array('username'=>$username,'password'=>password_hash($password,PASSWORD_BCRYPT),'email'=>$email));
            }else{
              echo 'Invalid email';
            }
          }else{
            echo 'Invalid password';
          }

        }else{
          echo 'Invalid username';
        }
      }else{
        echo 'Invalid Username';
      }

    }else{
      echo 'User already exists';
    }

    //echo 'Success';
  }
 ?>
<h1>Register</h1>
<form action="create-account.php" method="post">
  <input type="text" name="username" value="" placeholder="Username..."> <p></p>
  <input type="password" name="password" value="" placeholder="Password..."> <p></p>
  <input type="email" name="email" value="" placeholder="someone@domain.com"> <p></p>
  <input type="submit" name="createaccount" value="Create account">
</form>
