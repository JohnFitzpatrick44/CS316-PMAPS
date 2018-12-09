<html>
<body>

<?php
  if (!isset($_POST['text']) || !isset($_POST['name']) || !isset($_POST['type']) || !isset($_POST['longitude']) || !isset($_POST['lattitude'])) {
    echo "You need to fill out all form items. Please try again.";
    die();
  }
  $text = $_POST['text'];		// INPUT: text, type, name, x, y
  $name = $_POST['name'];
  $type = $_POST['type'];
  $lat = $_POST['longitude']; //REVERSED BUG
  $lng = $_POST['lattitude'];
?>

<?php
  try {
    include("/etc/php/my-pdo.php");
    $dbh = dbconnect();
  } catch (PDOException $e) {
    echo "Error connecting to the database: " . $e->getMessage() . "<br/>";
    die();
  }
  try {

    $pidst = $dbh->query('SELECT pid FROM Person WHERE name = \'' . $name . '\';');

    $dbh->beginTransaction();

    $pid = -1;

    if(!($pid = $pidst->fetch(PDO::FETCH_ASSOC))) {
      $pid = ($dbh->query('SELECT MAX(pid) AS max FROM Person;'))->fetch(PDO::FETCH_ASSOC)['max'] + 1; // Not efficient..?
    	$newPerson = $dbh->prepare('INSERT INTO Person VALUES (' . $pid . ', \'' . $name .'\', 0, \'' . $name . '\');');
    	$newPerson->execute();
    } else {
      $pid = $pid['pid'];
    }

    $lidst = $dbh->query('SELECT lid FROM Place WHERE longitude = ' . $lng . ' AND lattitude = ' . $lat . ';');

    $lid = -1;

    if(!($lid = $lidst->fetch(PDO::FETCH_ASSOC))) {
      $lid = ($dbh->query('SELECT MAX(lid) AS max FROM Place;'))->fetch(PDO::FETCH_ASSOC)['max'] + 1;
    	$newPlace = $dbh->prepare('INSERT INTO Place VALUES (' . $lid . ', ' . $lng . ', ' . $lat . ', ' . $pid . ');');
    	$newPlace->execute();
    } else {
      $lid = $lid['lid'];
    }

    $cid = ($dbh->query('SELECT MAX(cid) AS max FROM Comment;'))->fetch(PDO::FETCH_ASSOC)['max'] + 1;

    $st = $dbh->prepare('INSERT INTO Comment VALUES (' . $cid . ', \'' . $text . '\', \'' . strtolower($type) . '\', DEFAULT, DEFAULT, ' . $pid . ', ' . $lid . ');'); // cid, text, type, time, valid, pid, lid
    $st->execute();

    $dbh->commit();

  } catch (Exception $e) {
    echo $e->getMessage();
    $dbh->rollBack();
    die($e->getMessage());
  }

?>
Database updated. Return to PMAPS <a href="PMAPS_v6.php" >here.</a>
</body>
</html>
