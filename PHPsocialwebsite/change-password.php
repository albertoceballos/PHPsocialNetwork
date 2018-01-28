<?php
//include database class
  include('./classes/DB.php');
  //include login class
  include('./classes/Login.php');
  //token is not valid until logged in
  $tokenIsValid=False;
  if(Login::isLoggedIn()){ // if logged in

    if(isset($_POST['changepassword'])){ //if the change passsword button is checked
      $oldpassword = $_POST['oldpassword']; //obtain old password
      $newpassword = $_POST['newpassword']; //obtain new password
      $confirmpassword = $_POST['confirmpassword']; //confirm new password

      $userid = Login::isLoggedIn(); //get user id of currently logged in user

      //verify that old password is the actual password
      if(password_verify($oldpassword, DB::query('SELECT password FROM users WHERE id=:user_id',array(':user_id'=>$userid))[0]['password'])){
        //check if new password and confirm password match
        if($newpassword==$confirmpassword){
          //check that it fits password parameters
          if(strlen($newpassword)>=6 && strlen($newpassword<=60)){
            //change password and encrypt it
            DB::query('UPDATE users SET password=:newpassword WHERE id=:userid',array(':newpassword'=>password_hash($newpassword,PASSWORD_BCRYPT),':userid'=>$userid));
            echo 'Password changed';

          }
        }else{ //if password's don't match
          echo 'passwords don\'t match';
        }
      }else{ //if the old password is wrong
        echo 'Incorrect old password';
      }
    }
  }else{ //if not logged in
    if(isset($_GET['token'])){//check if token exists
    $token = $_GET['token']; //if it exists obtain the token
    if(DB::query('SELECT user_id FROM password_tokens WHERE token=:token',array(':token'=>sha1($token)))){ //check that the token is valid
      $user_id=DB::query('SELECT user_id FROM password_tokens WHERE token=:token',array(':token'=>sha1($token)))[0]['user_id']; //obtain user id
      $tokenIsValid=True; //make token valid
      if(isset($_POST['changepassword'])){ //check if change password is set
        $newpassword = $_POST['newpassword'];
        $confirmpassword = $_POST['confirmpassword'];

        if($newpassword==$confirmpassword){
          if(strlen($newpassword)>=6 && strlen($newpassword<=60)){
            //change password
            DB::query('UPDATE users SET password=:newpassword WHERE id=:userid',array(':newpassword'=>password_hash($newpassword,PASSWORD_BCRYPT),':userid'=>$userid));
            echo 'Password changed';
            //delete the password token
            DB::query('DELETE FROM password_tokens WHERE user_id=:user_id',array(':user_id'=>$user_id));
            }
          }else{
            echo 'passwords don\'t match';
          }

      }

    }else{
      die('Token invalid');
    }
  }else{
    die('Not logged in');
  }
  }

?>

<h1>Change your password</h1>
<form action="<?php if(!$tokenIsValid){ echo'change-password.php';}else{echo 'change-password.php?token='.$token.'';}?>" method="post">
  <?php if(!$tokenIsValid){ //if the token is not valid but user is logged in
    echo'<input type="password" name="oldpassword" value="" placeholder="current password..."><p></p> ';
  }?>
  <input type="password" name="newpassword" value="" placeholder="new password..."><p></p>
  <input type="password" name="confirmpassword" value="" placeholder="confirm password..."><p></p>
  <input type="submit" name="changepassword" value="Change password">
</form>
