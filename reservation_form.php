<?php
  if(isset($_POST['name'])) {
      //名前が送信されたら以下の処理を行う
      //この部分は変更してもいい
      
      //「予約フォーム」からの情報をそれぞれ変数に格納しておく↓
    $name=htmlspecialchars($_POST["name"], ENT_QUOTES);
    $time_number=htmlspecialchars($_POST["number"], ENT_QUOTES);
    $member=htmlspecialchars($_POST["member"], ENT_QUOTES);
    $day=htmlspecialchars($_POST["day"], ENT_QUOTES);
            //「予約フォーム」からの情報をそれぞれ変数に格納しておく↑
    $dsn="mysql:host=localhost;port=8889;dbname=reservation_calender;charset=utf8";
    $user="root";
    $pass="root";
    try{
      $db = new PDO($dsn,$user,$pass);
      $db->query("INSERT INTO reservation (name,time_number,member,day)
                  VALUES ('$name','$time_number','$member','$day')");
    }catch (Exception $e) {
      echo $e->getMessage() . PHP_EOL;
    }
    header("Location: calender_view.php");
    // "reservation_form.php（予約フォームがあったページ）"に戻る
    exit;
  }
?>

<form action="reservation_form.php" method="post">
  お名前
  <div><input type="text" name="name" placeholder="山田太郎"></div>
  電話番号
  <div><input type="tel" name="number" placeholder="08012349876"></div>
  人数
  <div><input name="member"></div>
  日付
  <div>
    <input type="date" name="day" list="daylist" value="<?php echo date("Y-m-d",strtotime($_REQUEST['ym'])) ?>">
  </div>
  <div class="submit">
      <input type="submit" value="送信">
  </div>
  <div class="reset">
      <input type="reset" value="リセット">
  </div>
</form>