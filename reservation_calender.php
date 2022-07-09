<!-----------カレンダープログラム--------------->
<?php

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
    $ym = $_GET['ym'];
}else{
    //今月の年月を表示
    $ym = date('Y-m');
}

//タイムスタンプ（どの時刻を基準にするか）を作成し、フォーマットをチェックする
//strtotime('Y-m-01')
$timestamp = strtotime($ym . '-01'); 
if($timestamp === false){//エラー対策として形式チェックを追加
    //falseが返ってきた時は、現在の年月・タイムスタンプを取得
    $ym = date('Y-m');
    $timestamp = strtotime($ym . '-01');
}

//今月の日付　フォーマット　例）2020-10-2
$today = date('Y-m-j');

//カレンダーのタイトルを作成　例）2020年10月
$html_title = date('Y年n月', $timestamp);//date(表示する内容,基準)

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
      $week .= '<td class="today">'. "<a href='reservation_form.php?date={$date}'>" . $day . $reservation;//今日の場合はclassにtodayをつける
    }elseif(strpos($reservation,'予約できません')){
      $week .= '<td>' . $day . $reservation;
    }elseif(reservation(date("Y-m-d",strtotime($date)),$reservation_array)){
        $week .= '<td>'. "<a href='reservation_form.php?date={$date}'>"  . $day . $reservation;
    }else{
        //上２つ以外なら
        $week .= '<td>'. "<a href='reservation_form.php?date={$date}'>"  . $day;
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
    <style>
      .container {
        font-family: 'Noto Sans', sans-serif;
          margin-top: 80px;
      }
        h3 {
            margin-bottom: 30px;
        }
        th {
            height: 30px;
            text-align: center;
        }
        td {
            height: 100px;
            width: 100px;
        }
        .today {
            background: orange;
        }
        th:nth-of-type(1), td:nth-of-type(1) {
            color: red;
        }
        th:nth-of-type(7), td:nth-of-type(7) {
            color: blue;
        }
        .holiday{
            color: red;
        }
        .green{
            color: green;
        }
        a:hover {
          text-decoration: none;
        }
        table td a{
            color : inherit;
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
        } 
    </style>
</head>

<body id="">
        <div class="container">
        <h3><a href="?ym=<?php echo $prev; ?>">&lt;</a><?php echo $html_title; ?><a href="?ym=<?php echo $next; ?>">&gt;</a></h3>
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

    
</body>
</html>