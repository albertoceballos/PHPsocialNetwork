<?php

  class Login{
    public static function isLoggedIn(){
      //checks if the cookie is set
      if(isset($_COOKIE['SNID'])){
        //checks if user_id matches token
        if(DB::query('SELECT user_id FROM login_tokens WHERE token=:token',array(':token'=>sha1($_COOKIE['SNID'])))){
          //gets user_id
          $userid = DB::query('SELECT user_id FROM login_tokens WHERE token=:token',array(':token'=>sha1($_COOKIE['SNID'])))[0]['user_id'];
          //if the cookie is set return id
          if(isset($_COOKIE['SNID_'])){
            return $userid;
          }else{//creates new token if expired
            $cstrong=True;
            $token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
            DB::query('INSERT INTO login_tokens (token,user_id) VALUES (:token,:user_id)',array(':token'=>sha1($token),':user_id'=>$userid));
            DB::query('DELETE FROM login_tokens WHERE token=:token', array(':token'=>sha1($_COOKIE['SNID'])));

            setcookie("SNID",$token,time() + 60*60*24*7,'/',NULL,NULL,TRUE);

            setcookie("SNID_",'1',time()+60*60*24*7,'/',NULL,NULL,TRUE);

            return $userid;
          }


        }
      }

      return false;
    }
  }

?>
