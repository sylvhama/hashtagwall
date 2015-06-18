<?php

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

if (isset($_GET['validate'])) {
  $sql = "UPDATE `post` SET `validated`= 1 WHERE `id_post` LIKE '" . $_GET['validate'] . "';";
  $result = $mysqli->query($sql);
}
if (isset($_GET['unvalidate'])) {
  $sql = "UPDATE `post` SET `validated`= -1 WHERE `id_post` LIKE '" . $_GET['unvalidate'] . "';";
  $result = $mysqli->query($sql);
}

$sql1 = 'SELECT * FROM `post` WHERE validated = 0 ORDER BY created_time DESC';
$result1 = $mysqli->query($sql1);

$sql2 = 'SELECT * FROM `post` WHERE validated = 1 ORDER BY created_time DESC';
$result2 = $mysqli->query($sql2);

$sql3 = 'SELECT * FROM `post` WHERE validated = -1 ORDER BY created_time DESC';
$result3 = $mysqli->query($sql3);

?>

<!DOCTYPE html>
<html>
  <head>
    <title>Hashtagwall - Post moderation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <h1 class="page-header">Hashtagwall - Post moderation</h1>
      <br>
      <h2>Pending</h2>
      <table class="table table-striped">
        <tr> 
          <th>Post ID</th>
          <th>User</th>
          <th>Text</th>
          <th>Network</th>
          <th>ID Call</th>
          <th>Date</th>
          <th>Validate</th>
          <th>Unvalidate</th>
        </tr>
      <?php while($r = mysqli_fetch_assoc($result1)): ?>
        <tr> 
          <td><?php echo $r['id_post']; ?></td>
          <td><?php echo $r['user']; ?></td>
          <td><?php echo $r['text']; ?></td>
          <td><?php echo $r['network']; ?></td>
          <td><?php echo $r['id_call']; ?></td>
          <td><?php echo $r['created_time']; ?></td>
          <td>
            <form method="get" action="./admin.php">
                <input type="submit" value="Validate">
                <input type="hidden" name="validate" value="<?php echo $r['id_post']; ?>">
            </form>
          </td>
          <td>
            <form method="get" action="./admin.php">
                <input type="submit" value="Unvalidate">
                <input type="hidden" name="unvalidate" value="<?php echo $r['id_post']; ?>">
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </table><br>
      <h2>Validated</h2>
      <table class="table table-striped">
        <tr>
          <th>Post ID</th>
          <th>User</th>
          <th>Text</th>
          <th>Network</th>
          <th>ID Call</th>
          <th>Date</th>
          <th>Unvalidate</th>
        </tr>
      <?php while($r = mysqli_fetch_assoc($result2)): ?>
        <tr>
          <td><?php echo $r['id_post']; ?></td>
          <td><?php echo $r['user']; ?></td>
          <td><?php echo $r['text']; ?></td>
          <td><?php echo $r['network']; ?></td>
          <td><?php echo $r['id_call']; ?></td>
          <td><?php echo $r['created_time']; ?></td>
          <td>
            <form method="get" action="./admin.php">
                <input type="submit" value="Unvalidate">
                <input type="hidden" name="unvalidate" value="<?php echo $r['id_post']; ?>">
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </table><br>
      <h2>Not validated</h2>
      <table class="table table-striped">
        <tr>
          <th>Post ID</th>
          <th>User</th>
          <th>Text</th>
          <th>Network</th>
          <th>ID Call</th>
          <th>Date</th>
          <th>Validate</th>
        </tr>
      <?php while($r = mysqli_fetch_assoc($result3)): ?>
        <tr>
          <td><?php echo $r['id_post']; ?></td>
          <td><?php echo $r['user']; ?></td>
          <td><?php echo $r['text']; ?></td>
          <td><?php echo $r['network']; ?></td>
          <td><?php echo $r['id_call']; ?></td>
          <td><?php echo $r['created_time']; ?></td>
          <td>
            <form method="get" action="./admin.php">
                <input type="submit" value="Validate">
                <input type="hidden" name="validate" value="<?php echo $r['id_post']; ?>">
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </table>
    </div>
  </body>
</html>
<?php
 $result1->close();
 $result2->close();
 $result3->close();
 $mysqli->close();
?>