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

<html>
<head><title>Drinker Information: <?= $drinker ?></title></head>
<body>

<h1>Drinker Information: <?=$drinker ?></h1>
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
    echo "Address: ", $myrow['address'];

    echo "<br/>\n";

    echo "Beer(s) liked: ";
    $st = $dbh->prepare("SELECT beer FROM Likes WHERE drinker=?");
    $st->execute(array($drinker));
    $count = 0;
    foreach ($st as $myrow) {
      $count++;
      if ($count > 1) {
        echo(", ");
      }
      echo $myrow['beer'];
    }
    if ($count == 0) {
      echo "none";
    }

    echo "<br/>\n";

    echo "Bar(s) frequented: ";
    $st = $dbh->prepare("SELECT bar, times_a_week FROM Frequents WHERE drinker=?");
    $st->execute(array($drinker));
    $count = 0;
    foreach ($st as $myrow) {
      $count++;
      if ($count > 1) {
        echo(", ");
      }
      echo $myrow['bar'], " (", $myrow['times_a_week'], " time(s) a week)";
    }
    if ($count == 0) {
      echo "none";
    }

    echo "<br/>\n";

  } catch (PDOException $e) {
    print "Database error: " . $e->getMessage() . "<br/>";
    die();
  }
?>
Go <a href='all-drinkers.php'>back</a>
or <a href='edit-drinker.php?drinker=<?= $drinker ?>'>edit</a> the information.
</body>
</html>
