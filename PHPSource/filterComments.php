<?php

  // filters: name,maxlat,minlat,maxlng,minlng,type(s),mintime,maxtime,trip(s)
  // array/field should be empty, not set, or "" for no input

  $logic = ' ';
  $relevant = false;

  if(isset($_POST['name']) && is_array($_POST['name'])) {
    $logic = $logic . '(';
    $name = $_POST['name'];
    foreach($name as $n) {
      $logic = $logic . 'name=\'' . $n . '\' OR ';
    }
    $logic = $logic . 'false) AND ';
  } else if($_POST['name'] != "" ) {
    $logic = $logic . 'name=\'' . $_POST['name'] . '\' AND ';
  }

  if(isset($_POST['type']) && is_array($_POST['type'])) {
    $logic = $logic . '(';
    $type = $_POST['type'];
    foreach($type as $t) {
      $logic = $logic . 'type=\'' . $t . '\' OR ';
    }
    $logic = $logic . 'false) AND ';
  } else if($_POST['type'] != "" ) {
    $logic = $logic . 'type=\'' . $_POST['type'] . '\' AND ';
  }

  if(isset($_POST['trip'])  && is_array($_POST['trip'])) {
    $logic = $logic . '(';
    $trip = $_POST['trip'];
    foreach($trip as $t) {
      $logic = $logic . 'trip=\'' . $t . '\' OR ';
    }
    $logic = $logic . 'false) AND ';
    $relevant = true;
  } else if($_POST['trip'] != "" ) {
    $logic = $logic . 'trip=\'' . $_POST['trip'] . '\' AND ';
    $relevant = true;
  }

  if(isset($_POST['maxlng']) && is_numeric($_POST['maxlng'])) {
    $logic = $logic . 'lattitude <= ' . $_POST['maxlng'] . ' AND ';
  }  

  if(isset($_POST['minlng']) && is_numeric($_POST['minlng'])) {
    $logic = $logic . 'lattitude >= ' . $_POST['minlng'] . ' AND ';
  }  

  if(isset($_POST['maxlat']) && is_numeric($_POST['maxlat'])) {
    $logic = $logic . 'longitude <= ' . $_POST['maxlat'] . ' AND ';
  }  

  if(isset($_POST['minlat']) && is_numeric($_POST['minlat'])) {
    $logic = $logic . 'longitude >= ' . $_POST['minlat'] . ' AND ';
  }  

  if(isset($_POST['mintime'])) {
    $logic = $logic . 'timestamp >= ' . $_POST['mintime'] . ' AND ';
  }  

  if(isset($_POST['maxtime'])) {
    $logic = $logic . 'timestamp <= ' . $_POST['maxtime'] . ' AND ';
  }  

  $logic = $logic . ' true;';
?>

<?php
  try {
    include("/etc/php/my-pdo.php");
    $dbh = dbconnect();
  } catch (PDOException $e) {
    die();
  }
  try {

    $query = '';

    if($relevant) {
      $query = 'SELECT DISTINCT name,text,type,timestamp,longitude,lattitude FROM Comment,Person,Place,RelevantFor WHERE Comment.pid = Person.pid AND Comment.lid = Place.lid AND Comment.cid = RelevantFor.cid AND ' . $logic;
    } else {
      $query = 'SELECT DISTINCT name,text,type,timestamp,longitude,lattitude FROM Comment,Person,Place WHERE Comment.pid = Person.pid AND Comment.lid = Place.lid AND ' . $logic;
    }

    $statement = $dbh->query($query);

    echo json_encode($statement->fetchAll(PDO::FETCH_ASSOC));

  } catch (Exception $e) {
    die();
  }

?>
