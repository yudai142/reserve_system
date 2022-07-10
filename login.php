<?php 
session_start();
require('dbconnect.php'); 

if($_COOKIE['email'] !== '') {
  $email = $_COOKIE['email'];
}

if(!empty($_POST)) {
  $email = $_POST['email'];
  if($_POST['email'] !== '' && $_POST['password'] !== '') {
    $login = $db->prepare('SELECT * FROM users WHERE email=? AND password=?');
    $login->execute(array($_POST['email'], $_POST['password']));
    $member = $login->fetch();
    if($member) {
      $_SESSION['id'] = $member['id'];
      $_SESSION['time'] = time();
      if($_POST['save'] === 'on') {
        setcookie('email', $_POST['email'], time()+360*86400);
      }
      header("Location: calender_view.php");
      exit;
    }else{
      $error['login'] = 'failed';
    }
  }else{
    $error['login'] = 'blank';
  }
}
?>

<!DOCTYPE html>
<html>

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-13xxxxxxxxx"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'UA-13xxxxxxxxx');
    </script>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>管理画面ログイン</title>

    <link rel="icon" href="favicon.ico">

    <!-- css -->
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login-wrapper" id="login">
        <div class="container">
            <div class="login">
                <div class="login-wrapper-title">
                    <h3>ログイン</h3>
                </div>
                <form action="" method="post" class="login-form">
                    <div class="form-group">
                        <p>メールアドレス</p>
                        <input type="email" name="email" size="35" maxlength="255" value="<?php print htmlspecialchars($email, ENT_QUOTES); ?>" required />
                        <?php if($error['login'] === 'blank'): ?>
                          <p class="error">"* メールアドレスとパスワードをご記入ください"</p>
                        <?php endif; ?>
                        <?php if($error['login'] === 'failed'): ?>
                          <p class="error">"* ログインに失敗しました。正しく入力してください"</p>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <p>パスワード</p>
                        <input type="password" name="password" size="35" maxlength="255" value="<?php print htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" required />
                    </div>
                    <button type="submit" class="btn btn-submit">ログイン</button>
                    <a href="calender_view.php" type="button" class="btn btn-submit">戻る</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>