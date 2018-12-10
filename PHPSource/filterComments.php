<?php

  // filters: name,maxlat,minlat,maxlng,minlng,type(s),mintime,maxtime,trip(s)
  // array/field should be empty, not set, or "" for no input

  function createString($var, $attr) {
    $returnStr = ' ';
    if(is_array($var)) {
      $returnStr = $returnStr . '(';
      foreach($var as $v) {
        $returnStr = $returnStr . $attr . '=\'' . $v . '\' OR ';
      }
      $returnStr = $returnStr . 'false) AND ';
    } else if($var != "" ) {
      $returnStr = $returnStr . $attr . '=\'' . $var . '\' AND ';
    }
    return $returnStr;
  }

  $logic = ' ';
  $relevant = false;

  if(isset($_POST['name'])) {
    $logic = $logic . createString($_POST['name'], 'name');
  }

  if(isset($_POST['type'])) {
    $logic = $logic . createString($_POST['type'], 'type');
  }

  if(isset($_POST['trip'])) {
    $logic = $logic . createString($_POST['trip'], 'trip');
    if($_POST['trip'] != "" || is_array($_POST['trip'])) {
      $relevant = true;
    }
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
