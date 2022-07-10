<!-----------カレンダープログラム--------------->
<?php
session_start();
require('dbconnect.php'); 

// ログイン中であるかの判定と、ログインユーザーIDがSQLに保存されているかの照合処理
if(isset($_SESSION["id"]) && $_SESSION["time"] + 3600 > time()){
  $_SESSION["time"] = time();
  $members = $db->prepare("SELECT * FROM users WHERE id=?");
  $members->execute(array($_SESSION["id"]));
  $member = $members->fetch();
}


function getreservation(){
    
  $dsn="mysql:host=localhost;port=8889;dbname=reservation_calender;charset=utf8";
  $user="root";
  $pass="root";
  try{
  $db = new PDO($dsn,$user,$pass);
  $ps = $db->query("SELECT * FROM reservation");
  }catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
  }
  $reservation_member = array();
  
  foreach($ps as $out){
      $day_out = strtotime((string) $out['day']);
  
      $member_out = (string) $out['member'];
      
      $reservation_member[date('Y-m-d', $day_out)] = $member_out;
          
  }
      ksort($reservation_member);
      return $reservation_member;
}

$reservation_array = getreservation();
//getreservation関数を$reservation_arrayに代入しておく

function reservation($date,$reservation_array){
    //カレンダーの日付と予約された日付を照合する関数
    
    if(array_key_exists($date,$reservation_array)){
        //もし"カレンダーの日付"と"予約された日"が一致すれば以下を実行する
        
        if($reservation_array[$date] >= 10){
            //予約人数が１０人以上の場合は以下を実行する
            
        $reservation_member = "<br/>"."<span class='green'>"."予約できません"."</span>";
        return $reservation_member;
            
    }
        
        else{
            //予約人数が１０人より少なければ以下を実行する
            
           $reservation_member = "<br/>"."<span class='green'>".$reservation_array[$date]."人"."</span>";
            //例：echo $reservation_member; → ３人
            //色を変えるためにspanでclassをつけた
            
        return $reservation_member; 
            
        }
    }
}

//タイムゾーンを設定
date_default_timezone_set('Asia/Tokyo');

//前月・次月リンクが選択された場合は、GETパラメーターから年月を取得
if(isset($_GET['ym'])){ 
    $ym = date("Y-m",strtotime($_GET['ym']));
    $timestamp = strtotime($_GET['ym']);
}else{
    //今月の年月を表示
    $ym = date('Y-m');
    //タイムスタンプ（どの時刻を基準にするか）を作成し、フォーマットをチェックする
    //strtotime('Y-m-01')
    $timestamp = strtotime($ym . '-01'); 
    if($timestamp === false){//エラー対策として形式チェックを追加
        //falseが返ってきた時は、現在の年月・タイムスタンプを取得
        $ym = date('Y-m');
        $timestamp = strtotime($ym . '-01');
    }
}



//今月の日付　フォーマット　例）2020-10-2
$today = date('Y-m-d');

//カレンダーのタイトルを作成　例）2020年10月
$html_title = date('Y-m-d', $timestamp);//date(表示する内容,基準)

//前月・次月の年月を取得
//strtotime(,基準)
$prev = date('Y-m', strtotime('-1 month', $timestamp));
$next = date('Y-m', strtotime('+1 month', $timestamp));


//該当月の日数を取得
$day_count = date('t', $timestamp);

//１日が何曜日か
$youbi = date('w', $timestamp);

//カレンダー作成の準備
$weeks = [];
$week = '';

//第１週目：空のセルを追加
//str_repeat(文字列, 反復回数)
$week .= str_repeat('<td></td>', $youbi);

for($day = 1; $day <= $day_count; $day++, $youbi++){
    
    
    
    
    $date = $ym . '-' . $day; 
    $ymd = date('Y-m-d', strtotime($date));
    //それぞれの日付をY-m-d形式で表示例：2020-01-23
    //$dayはfor関数のおかげで１日づつ増えていく
    
    //display_to_Holidays($date,$Holidays_array)の$dateに1/1~12/31の日付を入れる
    //比較してあったらdisplay_to_Holidaysメソッドによって$Holidays_array[$date]つまり$holidaysがreturnされる
    
    
    $reservation = reservation(date("Y-m-d",strtotime($date)),$reservation_array);

    
    
    if($today == $date && strpos($reservation,'予約できません')){
        //もしその日が今日なら
        $week .= '<td class="today">'. $day . $reservation;//今日の場合はclassにtodayをつける
    }elseif($today == $date){
      //もしその日が今日なら
      $week .= '<td class="today">'. "<a href='?ym={$ymd}'>" . $day . $reservation;//今日の場合はclassにtodayをつける
    }elseif(strpos($reservation,'予約できません')){
      $week .= '<td>' . "<a href='?ym={$ymd}'>" . $day . $reservation;
    }elseif(reservation(date("Y-m-d",strtotime($date)),$reservation_array)){
        $week .= '<td>'. "<a href='?ym={$ymd}'>"  . $day . $reservation;
    }else{
        //上２つ以外なら
        $week .= '<td>'. "<a href='?ym={$ymd}'>"  . $day;
    }
    $week .= '</a>' . '</td>';
    
    
    
    if($youbi % 7 == 6 || $day == $day_count){//週終わり、月終わりの場合
        //%は余りを求める、||はまたは
        //土曜日を取得
        
        if($day == $day_count){//月の最終日、空セルを追加
            $week .= str_repeat('<td></td>', 6 - ($youbi % 7));
        }
        
        $weeks[] = '<tr>' . $week . '</tr>'; //weeks配列にtrと$weekを追加
        
        $week = '';//weekをリセット
    }
}

    
?>
<!-----------カレンダープログラム--------------->




<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>PHPカレンダー</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Noto+Sans" rel="stylesheet">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js"></script>
    <link rel="stylesheet" href="style.css">
    <style>
    </style>
</head>

<body id="">
        <div class="container">
        <h3 class="title">面談予約フォーム</h3>
        <div class="date-form">
          <div class="icon-left">
            <a href="?ym=<?php echo $prev; ?>">
              <i class="fas fa-arrow-alt-circle-left fa-2x"></i>
            </a>
          </div>
          <label>
            <input type="month" id="month" value="<?php echo date("Y-m",strtotime($html_title)) ?>" />
          </label>
          <div class="icon-right">
            <a href="?ym=<?php echo $next; ?>">
              <i class="fas fa-arrow-alt-circle-right fa-2x"></i>
            </a>
          </div>
          <?php if(isset($member)):?>
            <span><a href="logout.php" class="logout-button">ログアウト</a></span>
            <span><a href="logout.php" class="list-button">予約リスト</a></span>
          <?php else:?>
            <span><a href="login.php" class="login-button">管理者ログイン</a></span>
          <?php endif;?>
        </div>
        <table class="table table-bordered">
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
            <?php
                foreach ($weeks as $week) {
                    echo $week;
                }
            ?>
        </table>
    </div>
    
    <?php if(isset($_REQUEST['ym']) && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_REQUEST['ym'])):?>
    <div class="container footer">
      <div class="card-list">
        <div class="card-list-item">
          <div class="card card-skin">
            <div class="card__textbox border">
            <div class="card__titletext"><?php echo date("Y-m-d",strtotime($html_title)) ?>　15:00〜15:30</div>
            </div>
            <div class="card__textbox input foot">
              <div class="card__titletext">
                受講者：<span><input type="text"></span>
              </div>
              <div class="card__overviewtext">
                  <input type="submit" value="予約する">
              </div>
            </div>
          </div>
        </div>

        <div class="card-list-item">
          <div class="card card-skin">
            <div class="card__textbox border">
            <div class="card__titletext"><?php echo date("Y-m-d",strtotime($html_title)) ?>　15:30〜16:00</div>
            </div>
            <div class="card__textbox foot">
              <div class="card__titletext">
                受講者：
                <span></span>
              </div>
              <div class="card__overviewtext">
                担当者：
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif;?>

    
</body>
  <script type="text/javascript">
    let url = new URL(window.location.href);
    let params = url.searchParams;

    function redirectValue() {
      let month = document.getElementById('month');
      location.href = "?ym=" + month.value;
    }

    let month = document.getElementById('month');

    if(params.get('month')){
      month.value = params.get('month');
    }

    month.addEventListener('change', redirectValue);
    </script>
</html>