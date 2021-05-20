<?php

ob_start();
session_start();

require_once "common_function.php";
require_once "test_form_data.php";

$form_update_data = [];

$update_param = ["test_form_id", "name", "post", "address", "birthday"];
foreach ($update_param as $p) {
    $form_update_data[$p] = $_POST[$p];
}

// var_dump($form_update_data);

// 共通バリデーション
$error_detail = error_validate($form_update_data);

// 誕生日の必須チェック
if ($form_update_data["birthday"] === "") {
    $error_detail["error_must_birthday"] = true;
} else {
    // 誕生日の型チェック
    if (substr_count($form_update_data["birthday"], "-") === 2) {
        [$yy, $mm, $dd] = explode("-", $form_update_data["birthday"]);
        if (checkdate((int) $mm, (int) $dd, (int) $yy) === false) {
            $error_detail["error_format_birthday"] = true;
        }
    } else {
        $error_detail["error_format_birthday"] = true;
    }
}

// csrf_tokenのエラーチェック
if (is_csrf_token_admin() === false) {
    $error_detail["error_token"] = true;
}

// var_dump($error_detail);

// エラーがあればadmin_data_upload.phpに戻す
if (!empty($error_detail)) {
    // エラー詳細と入力情報をセッションに詰める
    $_SESSION["output_buffer"] = $error_detail;
    $_SESSION["output_buffer"] += $form_update_data;
    header(
        "Location: ./admin_data_update.php?test_form_id=" .
            rawurldecode($form_update_data["test_form_id"])
    );
    exit();
}

// updateの処理
// db接続
$dbh = get_dbh();

// sql文
$sql =
    "UPDATE test_form SET name = :name, post = :post, address = :address, birthday = :birthday, updated = :updated WHERE test_form_id = :test_form_id";
$pre = $dbh->prepare($sql);

// bind
$pre->bindValue(":test_form_id", $form_update_data["test_form_id"]);
$pre->bindValue(":name", $form_update_data["name"]);
$pre->bindValue(":post", $form_update_data["post"]);
$pre->bindValue(":address", $form_update_data["address"]);
$pre->bindValue(":birthday", $form_update_data["birthday"]);
$pre->bindValue(":updated", date("Y-m-d h:i:s"));

$r = $pre->execute();
if ($r === false) {
    echo "システムでエラーが起こりました。";
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
      ユーザーデータをアップデートしました。
      <a href="./admin_data_list.php">一覧に戻る</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>

