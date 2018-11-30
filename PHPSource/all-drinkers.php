<html>
<head><title>All Drinkers</title></head>
<body>
<h1>All Drinkers</h1>

<?php
  try {
    // Including connection info (including database password) from outside
    // the public HTML directory means it is not exposed by the web server,
    // so it is safer than putting it directly in php code:
    include("/etc/php/my-pdo.php");
    $dbh = dbconnect();
  } catch (PDOException $e) {
    print "Error connecting to the database: " . $e->getMessage() . "<br/>";
    die();
  }
  try {
    $st = $dbh->query('SELECT * FROM Person');
    if (($myrow = $st->fetch())) {
?>
<form method="post" action="drinker-info.php">
Select a drinker below to view more information:<br/>
<?php
      do {
        // echo produces output HTML:
        echo "<input type='radio' name='drinker' value='" . $myrow['name'] . "'/>";
        echo $myrow['name'] . "<br/>";
      } while ($myrow = $st->fetch());
      // Below we will see the use of a "short open tag" that is equivalent
      // to echoing the enclosed expression.
?>
<?= $st->rowCount() ?> drinker(s) found in the database.<br/>
<input type="submit" value="GO!"/>
</form>
<?php
    } else {
      echo "There is no drinker in the database.";
    }
  } catch (PDOException $e) {
    print "Database error: " . $e->getMessage() . "<br/>";
    die();
  }
?>
</body>
</html>
