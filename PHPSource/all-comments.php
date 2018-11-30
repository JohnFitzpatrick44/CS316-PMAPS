<html>
<head><title>All Comments</title></head>
<body>
<h1>All Comments</h1>

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
    $st = $dbh->query('SELECT text, type, timestamp, pid FROM Comment');
    if (($myrow = $st->fetch())) {
      do {
        // echo produces output HTML:
        $pid = $myrow['pid'];
        $name = ($dbh->query('SELECT name FROM Person WHERE pid = ' . $pid))->fetch()['name'];
        echo "<div style=\"border:1px solid black;\">";
        echo "Commenter: " . $name . " Type: " . $myrow['type'] . " Time: " . $myrow['timestamp'] . "<br/>";
        echo $myrow['text'] . "<br/>";
        echo "</div>";
      } while ($myrow = $st->fetch());
      // Below we will see the use of a "short open tag" that is equivalent
      // to echoing the enclosed expression.
?>
<?= $st->rowCount() ?> comments(s) found in the database.<br/>
<?php
    } else {
      echo "There are no comments in the database.";
    }
  } catch (PDOException $e) {
    print "Database error: " . $e->getMessage() . "<br/>";
    die();
  }
?>
</body>
</html>
