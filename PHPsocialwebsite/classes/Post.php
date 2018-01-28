<?php
  class Post{
    public static function createPost($postBody, $loggedInUserId,$profileUserId){//get current user id
      if(strlen($postBody)>160 || strlen($postBody) < 1){ //limit post length
        die('post is too long or too short');
      }

      $topics = self::getTopics($postBody);

      if($loggedInUserId == $profileUserId){ //current user
        if(count(Notify::createNotify($postBody))!=0){
          foreach(Notify::createNotify($postBody) as $key=>$n){
            $sender = $loggedInUserId;
            $receiver = DB::query('SELECT id FROM users WHERE username=:username',array(':username'=>$key))[0]['id'];
            if($receiver!=0){
              DB::query('INSERT INTO notifications(type,receiver,sender,extra) VALUES (:type,:receiver,:sender,:extra)',array(':type'=>$n['type'],':receiver'=>$receiver,':sender'=>$sender,':extra'=>$n['extra']));
            }
          }
        }
        DB::query('INSERT INTO posts(user_id,body,posted_at,likes,topics) VALUES (:userid,:postbody,NOW(),0,:topics)',array(':userid'=>$profileUserId,':postbody'=>$postBody,':topics'=>$topics)); //create post in DB
      }else{
        die('Incorrect user');
      }
    }

    public static function getTopics($text){
      $text = explode(" ",$text);

      $topics = "";

      foreach($text as $word){
        if(substr($word,0,1)=="#"){
          $topics.= substr($word,1). ",";
        }
      }
      return $topics;
    }

    public static function createImagePost($postBody, $loggedInUserId, $profileUserId){ //create image post
      if(strlen($postBody)>160){ //if postbody is too short
        die('Incorrect lenght!');
      }
      $topics = self::getTopics($postBody);
      if($loggedInUserId == $profileUserId){ //only allows for same user
        if(count(Notify::createNotify($postBody) !=0)){
          foreach(Notify::createNotify($postBody) as $key =>$n){
            $sender = $loggedInUserId;
            $receiver = DB::query('SELECT id FROM users WHERE username=:username',array(':username'=>$key))[0]['id'];
            if($receiver!=0){
              DB::query('INSERT INTO notifications(type,receiver,sender,extra) VALUES(:type,:receiver,:sender,:extra)',array(':type'=>$n['type'],':receiver'=>$receiver,':sender'=>$sender,':extra'=>$n['extra']));
            }
          }
        }
        DB::query('INSERT INTO posts (user_id,body,posted_at,likes) VALUES (:userid,:post_body,NOW(),0)',array(':userid'=>$profileUserId,':post_body'=>$postBody)); //inser into post empty value
        $postid = DB::query('SELECT id FROM posts WHERE user_id=:userid ORDER BY ID DESC LIMIT 1',array(':userid'=>$loggedInUserId))[0]['id']; //get post id
        return $postid;
      }else{
        die('Incorrect User');
      }
    }

    public static function likePost($postId, $likerId){ //like post
      if(!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$postId,':userid'=>$likerId))){
        //increase likes
        DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid',array(':postid'=>$postId));
        //post_likes
        DB::query('INSERT INTO post_likes (post_id,user_id) VALUES(:postid,:userid)',array(':postid'=>$postId,':userid'=>$likerId));
        Notify::createNotify("",$postId);
      }else{
        DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid AND user_id=:userid',array(':postid'=>$postId,':userid'=>$likerId)); //dislike
        DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$postId,':userid'=>$likerId));
      }
    }

    public static function link_add($text){ //mentions
      $text = explode(" ",$text); //divide
      $newstring = "";
      foreach($text as $word){
        if(substr($word,0,1)=="@"){ //mention of user
          $newstring .= "<a href='profile.php?username".substr($word,1)."'>".htmlspecialchars($word)."</a> "; //set string
        }else if(substr($word,0,1)=="#"){
          $newstring .= "<a href='topics.php?topic=".substr($word,1)."'>".htmlspecialchars($word)."</a>";
        }else{
          $newstring .= htmlspecialchars($word)." ";
        }
      }
      return $newstring;
    }

    public static function displayPost($userid,$username,$loggedInUserId){
      //create template for posts
      $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC',array(':userid'=>$userid));
      //echo json_encode($dbposts);
      $posts = "";
      foreach($dbposts as $p){
        if(!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND user_id=:userid',array(':postid'=>$p['id'],':userid'=>$loggedInUserId))){
          $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='like' value='Like'>
                                        <span>".$p['likes']." likes</span>
                                ";
                                if ($userid == $loggedInUserId) {
                                        $posts .= "<input type='submit' name='deletepost' value='x' />";
                                }
                                $posts .= "
                                </form><hr /></br />
                                ";//supports images too
        }else{
          $posts .= "<img src='".$p['postimg']."'>".self::link_add($p['body'])."
                               <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                               <input type='submit' name='unlike' value='Unlike'>
                               <span>".$p['likes']." likes</span>
                               ";
                               if ($userid == $loggedInUserId) {
                                       $posts .= "<input type='submit' name='deletepost' value='x' />";
                               }
                               $posts .= "
                               </form><hr /></br />
                               ";
        }
      }
      return $posts;
    }
  }

?>
