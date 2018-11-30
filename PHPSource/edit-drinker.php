<html>
<head><title>Edit Drinker Information</title></head>
<body>

<?php
  if (!isset($_GET['drinker'])) {
    echo "You need to specify a drinker. Please <a href='all-drinkers.php'>try again</a>.";
    die();
  }
  $drinker = $_GET['drinker'];
  // In production code, you might want to "cleanse" the $drinker string
  // to remove potential hacks before doing something with it (e.g.,
  // passing it to the DBMS).  That said, using prepared statements
  // (see below for details) can prevent SQL injection attack even if
  // $drinker contains potentially malicious character sequences.
?>

<h1>Edit Drinker Information: <?= $drinker?></h1>
<form method="post" action="update-drinker.php">
<input type="hidden" name="drinker" value="<?= $drinker ?>"/>
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
    // One could construct a parameterized query manually as follows,
    // but it is prone to SQL injection attack:
    // $st = $dbh->query("SELECT address FROM Drinker WHERE name='" . $drinker . "'");
    // A much safer method is to use prepared statements:
    $st = $dbh->prepare("SELECT address FROM Drinker WHERE name=?");
    $st->execute(array($drinker));
    if ($st->rowCount() == 0) {
      die('There is no drinker named ' . $drinker . ' in the database.');
    } else if ($st->rowCount() > 1) {
      die('Something is wrong --- there are ' . $count . ' drinkers named ' . $drinker . ' in the database.');
    }
    $myrow = $st->fetch();
    echo "Address: <input type='text' name='address' value='",
         $myrow['address'],
         "' size='10' maxlength='20'/>";

    echo "<br/>\n";

    echo "Beer(s) liked: ";
    $st = $dbh->prepare("SELECT name, (SELECT COUNT(*) FROM Likes WHERE beer = name AND drinker=?) AS liked FROM Beer ORDER BY name");
    $st->execute(array($drinker));
    $count = 0;
    foreach ($st as $myrow) {
      $count++;
      echo "<input type='checkbox' name='beersLiked[", $count, "]' value='", $myrow['name'], "'";
      if ($myrow['liked'] > 0) {
        echo " checked";
      }
      echo "/>", $myrow['name'], " ";
    }

    echo "<br/>\n";

    echo "Bar(s) frequented:<br/>\n";
    $st = $dbh->prepare("SELECT name, (SELECT times_a_week FROM Frequents WHERE drinker=? AND bar = name) AS times FROM Bar ORDER BY name");
    $st->execute(array($drinker));
    $count = 0;
    foreach ($st as $myrow) {
      $count++;
      echo "<input type='hidden' name='bar[", $count, "]' value='", $myrow['name'], "'/>", $myrow['name'], ": ";
      echo "<input type='text' name='times[", $count, "]' value='";
      if (!$myrow['times']) {
        echo "0";
      } else {
        echo $myrow['times'];
      }
      echo "' size='1' maxlength='2'/><br/>\n";
    }

  } catch (PDOException $e) {
    print "Database error: " . $e->getMessage() . "<br/>";
    die();
  }
?>
<input type="reset" value="Reset"/>
<input type="submit" value="Submit Update"/>
</form>
</body>
</html>
