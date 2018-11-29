<?php
  function dbconnect() {
    $PDO_CONN = 'pgsql:host=localhost;dbname=base';
    $PDO_USER = '';
    $PDO_PASS = 'dbpasswd';
    $dbh = new PDO($PDO_CONN, $PDO_USER, $PDO_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $dbh;
  }
?>