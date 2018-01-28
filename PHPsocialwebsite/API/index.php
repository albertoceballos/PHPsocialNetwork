<?php
  require_once('DB.php'); //reference to database

  $db = new DB("localhost","PHPsocialnetwork","root","password");

  if($_SERVER['REQUEST_METHOD']=="GET"){ //GET REQUEST_METHOD
    if($_GET['url']=="auth"){ //if auth
    }else if($_GET['url']=="users"){ //if users

    }
  }else if($_SERVER['REQUEST_METHOD']=="POST"){ //POST REQUEST_METHOD
    if($_GET['url']=="auth"){ //if auth
      $postBody= file_get_contents("php://input"); //gets input

      $postBody= json_decode($postBody); //decode the obtained json file


      $username = $postBody->username; //obtain reference to username from decoded json
      $password = $postBody->password; //obtain reference to password from decoded json

      if($db->query('SELECT username FROM users WHERE username=:username',array(':username'=>$username))){ //check if valid username
        if(password_verify($password,$db->query('SELECT password FROM users WHERE  username=:username',array(':username'=>$username))[0]['password'])){ //check if valid password
          $cstrong = True; //type of encryption
          $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong)); //encrypt token
          $user_id= $db->query('SELECT id FROM users WHERE username=:username',array(':username'=>$username))[0]['id'];
          $db->query('INSERT INTO login_tokens(token,user_id) VALUES  (:token,:userid)',array(':token'=>sha1($token),':userid'=>$user_id));//get userid
          echo '{ "Token": "'.$token.'" }'; //echo token in return
        }else{
          http_response_code(401); //unauthorized response
        }
      }else{
        http_response_code(401); //unauthorized response
      }
    }
  }else if($_SERVER['REQUEST_METHOD']=="DELETE"){ //if request is delete
    if($_GET['url']=="auth"){ //if auth
      if(isset($_GET['token'])){ //if there is a token
        if($db->query('SELECT token FROM login_tokens WHERE token=:token',array(':token'=>sha1($_GET['token'])))){ //if token is valid
          $db->query('DELETE FROM login_tokens WHERE token=:token',array(':token'=>sha1($_GET['token']))); //delete token
          echo '{ "Status": "Success" }'; //if successful
          http_resonse_code(200); //ok response code
        }else{
          echo '{ "Error": "Invalid token"}'; //token not valid
          http_response_code(400); //bad request
        }
      }else{
        echo '{ "Error": "Malformed request"}';
        http_response_code(400); //bad request
      }
    }
  }else{
    http_response_code(405);//method not allowed response
    //unsopported method call
  }
?>
