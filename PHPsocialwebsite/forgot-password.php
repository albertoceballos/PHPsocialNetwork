<?php
  include('./classes/DB.php');
  include('./classes/Mail.php');

  //if reset password is set
  if(isset($_POST['resetpassword'])){
    $cstrong=True; //type of encryption
    $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong)); //create token
    $email = $_POST['email']; //get email
    $user_id=DB::query('SELECT id FROM users WHERE email=:email',array(':email'=>$email))[0]['id'];
    DB::query('INSERT INTO password_tokens(token,user_id) VALUES  (:token,:user_id)',array(':token'=>sha1($token),':user_id'=>$user_id)); //add password token
    echo 'Email sent';
    Mail::sendMail('Forgot password!',"<a href='http://localhost/PHPsocialnetwork/change-password.php?token=$token'>http://localhost/PHPsocialnetwork/change-password.php?token=$token</a>",$email);
    echo '<br />';
    //echo $token;
  }

?>

<h1>Forgot password</h1>
<form action="forgot-password.php" method="post">
  <input type="text" name="email" value="" placeholder="email"><p></p>
  <input type="submit"  name="resetpassword" value="Reset password">
</form>
