<?php

  class Notify{
    public static function createNotify($text="",$postid=0){
      $text = explode(" ",$text);
      $notify = array();
      foreach($text as $word){
        if(substr($word,0,1)=="@"){
          $notify[substr($word,1)] = array("type"=>1,"extra"=>' { "postbody": "'.htmlentities(implode($text, " ")).'" } "');
        }
      }
      if(count($text)==1 && $postid!=0){
        $temp = DB::query('SELECT posts.user_id AS receiver, post_likes.user_id as sender FROM posts, post_likes WHERE posts.id = post_likes.post_id AND posts.id=:postid',array(':postid'=>$postid));
        $receiver = $temp[0]['receiver'];
        $sender = $temp[0]['sender'];
        DB::query('INSERT INTO notifications(type,receiver,sender,extra) VALUES (:type,:receiver,:sender,:extra)',array(':type'=>2, ':receiver'=>$receiver,':sender'=>$sender,':extra'=>""));
      }
      return $notify;
    }

  }

?>
