<?php
  /********************
  Hashtag and Count sent via AJAX
  *********************/
  $data = file_get_contents("php://input");
  $objData = json_decode($data);

  if(!isset($objData->data->hashtag)) {
    $error = array("error" =>  "No hashtag value.");
    echo json_encode($error);
    exit();
  }
  if(!isset($objData->data->count)) {
    $error = array("error" =>  "No count value.");
    echo json_encode($error);
    exit();
  }

  $hashtag = $objData->data->hashtag;
  $count = $objData->data->count;


  /********************
  Time to wait before making a new call
  *********************/
  define("INTERVAL", 3);


  /********************
  Init DB and Insert Call if not too early
  *********************/
  $dbhost = 'localhost';
  $dbname = 'scotchbox';
  $dbuser = 'root';
  $dbpass = 'root';

  $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

  if ($mysqli->connect_errno) {
    $error=array("error" =>  $mysqli->connect_error);
    echo json_encode($error);
    exit();
  }

  $mysqli->set_charset("utf8");

  $sql = "SELECT MAX(time) AS max FROM `api_call`;";
  if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
      $obj = mysqli_fetch_assoc($result);
      $now = strtotime(date("Y-m-d H:i:s"));
      $last = strtotime($obj["max"]);
      $minutes = round(abs($now - $last) / 60,2);
      $result->close();

      if($minutes < INTERVAL) {
        $error = array("error" =>  "Please wait before next call.");
        echo json_encode($error);
        exit();
      }
    }
  }else {
    $error = array("error" =>  "SELECT MAX Call query error. " . $sql);
    echo json_encode($error);
    exit();
  }

  $id_call = -1;
  $sql = "INSERT INTO `api_call`(`time`) VALUES ('".date("Y-m-d H:i:s")."');";
  if ($result = $mysqli->query($sql)) {
    $id_call = $mysqli->insert_id;
  }else {
    $error = array("error" =>  "INSERT call query error. " . $sql);
    echo json_encode($error);
    exit();
  }

  /********************
  Test if post is already in the DB
  *********************/
  function alreadyPost($id) {
    $mysqli = $GLOBALS['mysqli'];
    $sql = "SELECT id_post FROM `post` WHERE id_post LIKE '".$id."';";
    if ($result = $mysqli->query($sql)) {
      if ($result->num_rows > 0) {
        $result->close();
        return true;
      }else {
        $result->close();
        return false;
      }
    }else {
      $error = array("error" =>  "SELECT alreadyPost query error. " . $sql);
      echo json_encode($error);
      exit();
    }
  }


  /********************
  Insert a post in the DB
  *********************/
  function addPost($post) {
    $mysqli = $GLOBALS['mysqli'];
    $sql = "INSERT INTO `post`(`id_post`, `user`, `text`, `img`, `url`, `network`, `id_call`, `created_time`) VALUES ('".$post['id']."', '".addslashes($post['user'])."', '".addslashes($post['text'])."', '".$post['img']."', '".$post['url']."', '".$post['network']."', ".$post['id_call'].", '".date("Y-m-d H:i:s", $post['created_time'])."');";
    if ($result = $mysqli->query($sql)) {
      return true;
    }else {
      $error = array("error" =>  "INSERT call query error. " . $sql);
      echo json_encode($error);
      exit();
    }
  }


  /********************
  Instagram and Twitter Call
  *********************/
  require_once('./instagram.php');
  require_once('./twitter.php');

  $instas = getInstas($hashtag, $count);
  $tweets = getTweets($hashtag, $count);

  $bugInstas = false;
  $bugTweets = false;

  if(!property_exists($instas, 'data')) {
    $bugInstas = true;
  }else {
    $instas = $instas->data;
  }
  if(!property_exists($tweets, 'statuses')) {
    $bugTweets = true;
  }else {
    $tweets = $tweets->statuses;
  }

  if($bugInstas && $bugTweets) {
    $error = array("error" =>  "No feed.");
    echo json_encode($error);
    exit();
  }


  /********************
  JSON preparation
  *********************/
  $feed = array();
  if(!$bugTweets) {
    foreach ($tweets as $tweet) {
      if(!alreadyPost($tweet->id_str)) {
        $row = array(
          'id' => $tweet->id_str,
          'user' => $tweet->user->screen_name,
          'text' => $tweet->text,
          'img' => '',
          'url' => 'https://twitter.com/'.$tweet->user->screen_name.'/status/'.$tweet->id_str,
          'network' => 'twitter',
          'id_call' => $id_call,
          'created_time' => $tweet->created_time
        );
        $feed[] = $row;
      }
    }
  }
  if(!$bugInstas) {
    foreach ($instas as $insta) {
      if(!alreadyPost($insta->id)) {
        $text = '';
        if (property_exists($insta, 'caption')) $text = $insta->caption->text;
        $row = array(
          'id' => $insta->id,
          'user' => $insta->user->username,
          'text' => $text,
          'img' => $insta->images->standard_resolution->url,
          'url' => $insta->link,
          'network' => 'instagram',
          'id_call' => $id_call,
          'created_time' => $insta->created_time
        );
        $feed[] = $row;
      }
    }
  }

  foreach ($feed as $post) {
    addPost($post);
  }
  echo json_encode($feed);
  exit();
?>