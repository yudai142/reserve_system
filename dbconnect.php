<?php
$filename = './env.php';

if (!file_exists($filename)){
  //For Heroku
  $url = parse_url(getenv("DATABASE_URL"));
  try {
    $db = new PDO("pgsql:" . sprintf(
      "host=%s;port=%s;user=%s;password=%s;dbname=%s",
      $url["host"],
      $url["port"],
      $url["user"],
      $url["pass"],
      ltrim($url["path"], "/")
    ));
  } catch(PDOException $e) {
      print('DB接続エラー：' . $e->getMessage());
  }
}else{
  require_once 'env.php';
  // ini_set('display_errors', true);
  $host = DB_HOST;
  $user = DB_USER;
  $pass = DB_PASS;
  $dbc = DB_NAME;
  $dsn = "mysql:host=$host;dbname=$dbc;charset=utf8mb4";

  try {
    $db = new PDO($dsn, $user, $pass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
  } catch(PDOException $e) {
      print('DB接続エラー：' . $e->getMessage());
  }
}
?>