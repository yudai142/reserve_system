<?php
try {
    $db = new PDO('mysql:dbname=reservation_calender;host=localhost;port=8889;charset=utf8',"root","root");
} catch(PDOException $e) {
    print('DB接続エラー：' . $e->getMessage());
}
?>