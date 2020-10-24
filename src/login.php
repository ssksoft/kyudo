<?php
require 'dbaccess.php';
session_start();

// Initialize
try {
  // Read parameters from configuration file as .ini
  $params = parse_ini_file('conf/kyudo.ini', true);
  if ($params === false) {
    throw new \Exception("Error reading ini configuration file");
  }

  //DB connection
  $pdo = connect($params['database']);
} catch (\PDOException $e) {
  error_log("\PDO::Exception" . $e->getMessage());
  echo ($e->getMessage());
  echo ("... Under maintenance");
  goto end;
}

// POSTのvalidate
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  echo '入力された値が不正です。';
  return false;
}
// DB内でPOSTされたメールアドレスを検索
$email = $_POST['email'];

$row = confirm_email($pdo, $email);
if (isset($row['email'])) {
  // Donothing
} else {
  echo 'メールアドレス又はパスワードが間違っています。';
  return false;
}

// パスワード確認後sessionにメールアドレスを渡す
if (password_verify($_POST['password'], $row['password'])) {
  session_regenerate_id(true);
  $_SESSION['EMAIL'] = $row['email'];
  echo 'ログインしました';
  echo "<a href='/kyudo'>記録はこちら。</a>";
} else {
  echo 'メールアドレス又はパスワードが間違っています。';
  return false;
}

end:
