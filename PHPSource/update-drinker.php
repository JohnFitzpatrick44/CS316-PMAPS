<html>
<head><title>Update Drinker Information</title></head>
<body>

<?php
  if (!isset($_POST['drinker'])) {
    echo "You need to specify a drinker. Please <a href='all-drinkers.php'>try again</a>.";
    die();
  }
  $drinker = $_POST['drinker'];
  // In production code, you might want to "cleanse" the $drinker string
  // to remove potential hacks before doing something with it (e.g.,
  // passing it to the DBMS).  That said, using prepared statements
  // (see below for details) can prevent SQL injection attack even if
  // $drinker contains potentially malicious character sequences.
?>

<h1>Update Drinker Information: <?= $drinker ?></h1>
<?php
  try {
    // Including connection info (including database password) from outside
    // the public HTML directory means it is not exposed by the web server,
    // so it is safer than putting it directly in php code:
    include("/etc/php/pdo-beers.php");
    $dbh = dbconnect();
  } catch (PDOException $e) {
    print "Error connecting to the database: " . $e->getMessage() . "<br/>";
    die();
  }
  try {

    $dbh->beginTransaction();

    // One could construct a parameterized query manually as follows,
    // but it is prone to SQL injection attack:
    // $dbh->exec("UPDATE Drinker SET address = " . $_POST['address'] . "' WHERE name = '" . $drinker . "'");
    $st = $dbh->prepare("UPDATE Drinker SET address = ? WHERE name = ?");
    $st->execute(array($_POST['address'], $drinker));

    // To update Likes, first delete all rows, then insert new rows one by one:
    $st = $dbh->prepare("DELETE FROM Likes WHERE drinker = ?");
    $st->execute(array($drinker));
    $index = array_keys($_POST['beersLiked']);
    $st = $dbh->prepare("INSERT INTO Likes VALUES(?, ?)");
    for ($i=0; $i<count($index); $i++) {
       $beer = $_POST['beersLiked'][$index[$i]];
       $st->execute(array($drinker, $beer));
    }

    // To update Frequents, first delete all rows, then insert new rows one by one:
    $index = array_keys($_POST['bar']);
    if (count($index) != count(array_keys($_POST['times']))) {
      throw new Exception('POST data not consistent');
    }
    $st = $dbh->prepare("DELETE FROM Frequents WHERE drinker = ?");
    $st->execute(array($drinker));
    $st = $dbh->prepare("INSERT INTO Frequents VALUES(?, ?, ?)");
    for ($i=0; $i<count($index); $i++) {
      $bar = $_POST['bar'][$index[$i]];
      $times = $_POST['times'][$index[$i]];
      if ($times > 0) {
        $st->execute(array($drinker, $bar, $times));
      }
    }

    $dbh->commit();

  } catch (Exception $e) {
    $dbh->rollBack();
    die($e->getMessage());
  }

?>
Database updated.
Click <a href='all-drinkers.php'>here</a> for the updated database.
</body>
</html>
