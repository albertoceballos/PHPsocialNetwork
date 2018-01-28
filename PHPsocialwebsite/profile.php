<?php
  //include db class
  include('./classes/DB.php');
  //include login class
  include('./classes/Login.php');

  include('./classes/Post.php');

  include('./classes/Image.php');

  include('./classes/Notify.php');
  //set username default is empty
  $username = "";
  //is following set to false
  $isFollowing = False;
  //default not verified
  $verified = False;
  if(isset($_GET['username'])){//if username is set
    //check that is a valid username
    if(DB::query('SELECT username FROM users WHERE username=:username',array(':username'=>$_GET['username']))){
      //get username from database
      $username = DB::query('SELECT username FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['username'];
      //get id from friend?
      $userid= DB::query('SELECT id FROM users WHERE username=:username',array(':username'=>$username))[0]['id'];
      //follower id is the user current id
      $followerid = Login::isLoggedIn();
      //check if verified
      $verified = DB::query('SELECT verified FROM users WHERE username=:username',array(':username'=>$username))[0]['verified'];
      //if follow
      if(isset($_POST['follow'])){
        //check that the id that you want to follow and your id are not the same
        if($userid != $followerid){
          //check that is valid follower id
          if(!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid',array(':userid'=>$userid))){
            //if your id is the id of verified
            if($followerid==57){ //verified
              DB::query('UPDATE users SET verified=:verified WHERE id=:userid',array(':verified'=>1,':userid'=>$userid));
            }
            //make person follow another
            DB::query('INSERT INTO followers (user_id, follower_id) VALUES (:userid,:followerid)',array(':userid'=>$userid,':followerid'=>$followerid));
          }else{
            echo 'Already following';
          }
          //is following is true
          $isFollowing=True;
        }
      }
      //if clicked unfollow
      if(isset($_POST['unfollow'])){
        //check that you don't want to unfolow yourself
        if($userid != $followerid){
          //check if valid follower id
          if(DB::query('SELECT follower_id FROM followers WHERE user_id=:userid',array(':userid'=>$userid))){
            if($followerid==57){ //if your id is verified account id then unverify
              DB::query('UPDATE users SET verified=:verified WHERE id=:userid',array(':verified'=>0,':userid'=>$userid));
            }
            //delete follower
            DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid',array(':userid'=>$userid,':followerid'=>$followerid));
          }
          $isFollowing=False;
        }
      }
      //if following
      if(DB::query('SELECT follower_id FROM followers WHERE user_id=:userid',array(':userid'=>$userid))){
        $isFollowing=True;
      }

      if(isset($_POST['deletepost'])){
        if(DB::query('SELECT id FROM posts WHERE id=:postid AND user_id=:userid',array(':postid'=>$_GET['postid'],':userid'=>$followerid))){
          DB::query('DELETE FROM posts WHERE id=:postid AND user_id=:userid',array(':postid'=>$_GET['postid'],':userid'=>$followerid));
          DB::query('DELETE FROM post_likes WHERE post_id=:postid',array(':postid'=>$_GET['postid']));
          echo 'Post deleted!';
        }
      }

      if(isset($_POST['post'])){ //post
        $postBody = $_POST['postbody']; //get post body
        $loggedInUserId = Login::isLoggedIn();
        if($_FILES['postimg']['size']==0){ // if image
          Post::createPost($postBody,$loggedInUserId,$userid); //create post
        }else{
          $postid= Post::createImagePost($postBody,$loggedInUserId,$userid); //get post id and create image posts
          Image::uploadImage('postimg',"UPDATE posts SET postimg=:postimg WHERE id=:postid",array(':postid'=>$postid)); //upload image
        }
      }
      if(isset($_GET['postid'])){ //get current postid
        Post::likePost($_GET['postid'],$followerid); //like post
      }

      $posts=Post::displayPost($userid,$username,$followerid); //get posts

    }else{
      die('User not found');
    }
  }

?>

<h1><?php echo $username;?>'s Profile<?php if($verified){echo'--Verified';}else{echo'--not verified';}?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
  <?php
    if($userid != $followerid){
      if($isFollowing){
        echo '<input type="submit" name="unfollow" value="Unfollow" >';
      }else{
        echo '<input type="submit" name="follow" value="Follow" />';
      }
    }
  ?>
</form>
<form action="profile.php?username=<?php echo $username?>" method="post" enctype="multipart/form-data">
  <textarea name="postbody" rows="8" cols="80"></textarea>
  <br />Upload an image:
  <input type="file" name="postimg">
  <input type="submit" name="post" value="Post">
</form>
<div class="posts">
  <?php
  echo $posts;
  ?>
</div>
