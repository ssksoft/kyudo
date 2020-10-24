<?php
require_once('config.php');

session_start();

// POSTのvalidate
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
  echo '入力された値が不正です。';
  return false;
}
// DB内でPOSTされたメールアドレスを検索
$email = $_POST['email'];
if (confirm_email($pdo, $email)) {
} else {
  return false;
}

// パスワード確認後sessionにメールアドレスを渡す
if (password_verify($_POST['password'], $row['password'])) {
  session_regenerate_id(true);
  $_SESSION['EMAIL'] = $row['email'];
  echo 'ログインしました';
} else {
  echo "test2";
  echo 'メールアドレス又はパスワードが間違っています。';
  return false;
}
