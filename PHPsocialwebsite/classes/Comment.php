<?php
  class Comment{
    public static function createComment($commentBody, $postid,$userid){
      if(strlen($commentBody)>160 || strlen($commentBody)<1){
        die('Comment to short or too long');
      }
      if(!DB::query('SELECT id FROM posts WHERE id=:postid',array(':postid'=>$postid))){
        echo 'Invalid post ID';
      }else{
        DB::query('INSERT INTO comments(comment,user_id,posted_at,post_id) VALUES(:comment,:userid,NOW(),:postid)',array(':comment'=>$commentBody,':userid'=>$userid,':postid'=>$postid));
      }
    }

    public static function displayComments($postid){
      echo 'Display comments';
      $comments = DB::query('SELECT comments.comment, users.username FROM comments, users WHERE post_id=:postid AND comments.user_id= users.id',array(':postid'=>$postid));
      foreach($comments as $comment){
        echo $comment['comment']." ~ ".$comment['username']."<hr />";
      }
    }
  }
?>
