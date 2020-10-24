<?php
ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);

session_start();

// ログイン済みの場合
if (isset($_SESSION['EMAIL'])) {
  echo 'ようこそ' . h($_SESSION['EMAIL']) . "さん<br>";
  echo "<a href='/logout.php'>ログアウトはこちら。</a>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="utf-8">
  <title>Login</title>
</head>

<body>
  <h1>ようこそ、ログインしてください。</h1>
  <form action="login.php" method="post">
    <label for="email">email</label>
    <input type="emall" name="email">
    <label for="password">password</label>
    <input type="password" name="password">
    <button type="submit">Sign In!</button>
  </form>
  <h1>初めての方はこちら</h1>
  <form action="signUp.php" method="post">
    <label for="email">email</label>
    <input type="email" name="email">email
    <label for="password">password</label>
    <input type="password" name="password">
    <button type="submit">Sign Up!</button>
    <p>※パスワードは半角英数字をそれぞれ1文字以上含んだ、8文字以上で設定してください。</p>
  </form>
</body>

</html>


<?php
if (isset($_POST['email']) && isset($_POST['password'])) {
} else {
  goto end;
}
function h($s)
{
  return htmlspecialchars($s, ENT_QUOTES, 'utf-8');
}

require_once('config.php');

// データベースへ接続、テーブルがない場合は作成

try {
  $pdo = new PDO(DSN, DB_USER, DB_PASS);
} catch (Exception $e) {
  echo $e->getMessage() . PHP_EOL;
}

// POSTのValidate
if ((!$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
  echo '入力された値が不正です。';
  return false;
}

// パスワードの正規表現
if (preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{8,100}+\z/i', $_POST['password'])) {
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
} else {
  echo 'パスワードは半角英数字をそれぞれ1文字以上含んだ8文字以上で設定してください。';
  echo 'よくないパスワード';
  goto end;
}

// 登録処理
try {
  $stmt = $pdo->prepare("insert into user_data_tbl(email, password) values(:email, :password)");
  $stmt->bindValue(':email', pg_escape_string($email));
  $stmt->bindValue(':password', pg_escape_string($password));

  $stmt->execute();
  echo "登録完了";
} catch (\Exception $e) {
  echo "登録済みのメールアドレスです。";
}


end:

?>