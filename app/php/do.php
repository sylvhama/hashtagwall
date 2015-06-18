<?php

isset($_GET['r'])	or die('Forbidden');

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

$method = $_GET['r'];

switch ($method) {
	case 'selectPosts':
		echo selectPosts();
	  break;
	case 'selectPostsUsers':
		echo selectPostsUsers();
	  break;
	case 'selectPostsByCall':
		echo selectPosts();
	  break;
	case 'selectLastCall':
		echo selectLastCall();
	  break;
	case 'selectPostsNotValidated':
		echo selectPostsNotValidated();
	  break;
	case 'addPost':
		echo addPost();
	  break;
	case 'addCall':
		echo addCall();
	  break;
	default:
		$error = array("error" =>  "Undefined function.");
    echo json_encode($error);
	  break;
}

$mysqli->close();

function selectPosts() {
  $mysqli = $GLOBALS['mysqli'];
  $sql = "SELECT * FROM `post` WHERE validated=1 ORDER BY created_time DESC;";
  if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        $obj[] = $row;
      }
      $result->close();
      return json_encode($obj);
    }else {
      $obj = array();
      $result->close();
      return json_encode($obj);
    }
  }else {
    $error = array("error" =>  "SELECT posts query error.");
    return json_encode($error);
  }
}

function selectPostsUsers() {
  $mysqli = $GLOBALS['mysqli'];
  $sql = "SELECT DISTINCT user FROM `post` WHERE validated=1";
  if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        $obj[] = $row;
      }
      $result->close();
      return json_encode($obj);
    }else {
      $obj = array();
      $result->close();
      return json_encode($obj);
    }
  }else {
    $error = array("error" =>  "SELECT postsUsers query error.");
    return json_encode($error);
  }
}

function selectPostsByCall() {
  $mysqli = $GLOBALS['mysqli'];
  $sql = "SELECT * FROM `post` WHERE id_call=".addslashes($objData->data->idCall).";";
  if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        $obj[] = $row;
      }
      $result->close();
      return json_encode($obj);
    }else {
      $obj = array();
      $result->close();
      return json_encode($obj);
    }
  }else {
    $error = array("error" =>  "SELECT post query error.");
    return json_encode($error);
  }
}

function selectLastCall() {
  $mysqli = $GLOBALS['mysqli'];
  $sql = "SELECT id_call, MAX(time) AS max FROM `api_call`;";
  if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
      $obj = mysqli_fetch_assoc($result);
      $result->close();
      return json_encode($obj);
    }else {
      $obj = array();
      $result->close();
      return json_encode($obj);
    }
  }else {
    $error = array("error" =>  "SELECT last call query error.");
    return json_encode($error);
  }
}

function selectPostsNotValidated() {
  $mysqli = $GLOBALS['mysqli'];
  $sql = "SELECT id_post FROM `post` WHERE validated!=1;";
  if ($result = $mysqli->query($sql)) {
    if ($result->num_rows > 0) {
      while($row = mysqli_fetch_assoc($result)) {
        $obj[] = $row;
      }
      $result->close();
      return json_encode($obj);
    }else {
      $obj = array();
      $result->close();
      return json_encode($obj);
    }
  }else {
    $error = array("error" =>  "SELECT postNotValidated query error.");
    return json_encode($error);
  }
}

function addPost() {
	$data = file_get_contents("php://input");
	$objData = json_decode($data);

	$mysqli = $GLOBALS['mysqli'];
  $sql = "INSERT INTO `post`(`user`, `text`, `img`, `url`, `id_network`, `id_call`, `created_time`) VALUES (".addslashes($objData->data->user)."', '".addslashes($objData->data->text)."', '".addslashes($objData->data->img)."', '".addslashes($objData->data->url)."', ".addslashes($objData->data->network).", ".addslashes($objData->data->idCall).", ".addslashes($objData->data->createdTime).";";
  if ($result = $mysqli->query($sql)) {
    $id = $mysqli->insert_id;
    return $id;
  }else {
    $error = array("error" =>  "INSERT post query error. " . $sql);
    return json_encode($error);
  }
}

function addCall() {
	$data = file_get_contents("php://input");
	$objData = json_decode($data);

	$mysqli = $GLOBALS['mysqli'];
  $sql = "INSERT INTO `api_call`(`time`) VALUES ('".date("Y-m-d H:i:s")."');";
  if ($result = $mysqli->query($sql)) {
    $id = $mysqli->insert_id;
    return $id;
  }else {
    $error = array("error" =>  "INSERT call query error. " . $sql);
    return json_encode($error);
  }
}

?>