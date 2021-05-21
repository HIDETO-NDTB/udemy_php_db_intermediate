<?php

require_once "common_function.php";
ob_start();
session_start();

$delete_data = $_POST;

// csrf_tokenのチェック
if (is_csrf_token_admin() === false) {
    $_SESSION["output_buffer"]["csrf_token"] = true;
    header("Location: ./admin_data_list.php");
    exit();
}

// db接続
$dbh = get_dbh();

// sql文
$sql = "DELETE from test_form WHERE test_form_id = :test_form_id";
$pre = $dbh->prepare($sql);

// bind
$pre->bindValue(":test_form_id", $delete_data["test_form_id"], PDO::PARAM_INT);

$r = $pre->execute();
if ($r === false) {
    echo "システムにエラーが起きました。";
    exit();
}

unset($_SESSION["output_buffer"]);
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
  <div class="container" style="margin-top: 30px;">
    データを削除しました<br>
    <a href="./admin_data_list.php">リストに戻る</a>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>


