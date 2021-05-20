<?php

ob_start();

require_once "common_function.php";
require_once "test_form_data.php";

// 引数がない場合の為のエラー回避 @と変な引数が入った場合の型指定を行う
$test_form_id = (string) @$_GET["test_form_id"];

// 空文字の場合はリストに戻す
if ($test_form_id === "") {
    header("Location: ./admin_data_list.php");
    exit();
}
// var_dump($test_form_id);

// idからデータを取得し変数に格納
$d = get_test_form($test_form_id);

if (empty($d)) {
    header("Location: ./admin_data_list.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <title>DB講座中級 管理者画面</title>
</head>
<body>
  <div class="container">
  <h4 style="margin-top: 50px; margin-bottom: 20px;">フォーム内容詳細</h4>
  <table class="table table-hover">
    <tr>
      <td>form id</td>
      <td><?php echo h($d["test_form_id"]); ?></td>
    </tr>
    <tr>
      <td>名前</td>
      <td><?php echo h($d["name"]); ?></td>
    </tr>
    <tr>
      <td>郵便番号</td>
      <td><?php echo h($d["post"]); ?></td>
    </tr>
    <tr>
      <td>住所</td>
      <td><?php echo h($d["address"]); ?></td>
    </tr>
    <tr>
      <td>誕生日</td>
      <td><?php echo h($d["birthday"]); ?></td>
    </tr>
    <tr>
      <td>作成日時</td>
      <td><?php echo h($d["created"]); ?></td>
    </tr>
    <tr>
      <td>修正日時</td>
      <td><?php echo h($d["updated"]); ?></td>
    </tr>
  </table>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
