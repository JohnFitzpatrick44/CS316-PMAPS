<html>
<head><title>Update Drinker Information</title></head>
<body>

<?php
  if (!isset($_POST['text'])) {
    echo "You need to specify a drinker. Please try again.";
    die();
  }
  $text = $_POST['text'];		// INPUT: text, type, name, x, y
  $name = $_POST['name'];
  $type = $_POST['type'];
  $x = $_POST['x'];
  $y = $_POST['y'];
?>

<h1>Update Drinker Information: <?= $text ?></h1>
<?php
  try {
    include("/etc/php/my-pdo.php");
    $dbh = dbconnect();
  } catch (PDOException $e) {
    print "Error connecting to the database: " . $e->getMessage() . "<br/>";
    die();
  }
  try {

    $pidst = $dbh->query('SELECT pid FROM Person WHERE name = $name');


    $dbh->beginTransaction();

    $pid = -1;

    if(!($pid = $pidst->fetch())) {
    	$newPerson = $dbh->prepare("INSERT INTO Person VALUES (DEFAULT, ?, 0)");
    	$newPerson->execute(array($name));
    	$pid = ($dbh->query('SELECT pid FROM Person WHERE name = $name'))->fetch();	// Not efficient..?
    }

    $lidst = $dbh->query('SELECT lid FROM Place WHERE longitude = $x AND lattitude = $y');

    $lid = -1;

    if(!($lid = $lidst->fetch())) {
    	$newPlace = $dbh->prepare("INSERT INTO Place VALUES (DEFAULT, ?, ?, ?)");
    	$newPlace->execute(array($x, $y, $pid));
    	$lid = ($dbh->query('SELECT lid FROM Place WHERE longitude = $x AND lattitude = $y'))->fetch();	// Not efficient..?
    }



    $st = $dbh->prepare("INSERT INTO Comment VALUES (DEFAULT, ?, ?, DEFAULT, DEFAULT, ?, ?)"); // cid, text, type, time, valid, pid, lid
    $st->execute(array($text, $type, $pid, $lid));

    $dbh->commit();

  } catch (Exception $e) {
    $dbh->rollBack();
    die($e->getMessage());
  }

?>
Database updated.
</body>
</html>
