<?php
  class DB{
      private static function connect(){
        //creates connection to database
          $pdo = new PDO('mysql:host=localhost;dbname=phpsocialnetwork;charset=utf8','root','password',array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION));

        return $pdo;
      }

      public static function query($query,$params=array()){
        //prepares the query
        $statement = self::connect()->prepare($query);
        //executes the query
        $statement->execute($params);
        //if the query is select then return information
        if(explode(' ',$query)[0] == 'SELECT'){
          $data = $statement->fetchAll();
          return $data;
        }
        //$data = $statement->fetchAll();
        //return $data;
      }
  }

?>
