<?php

// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();
// どこでバリデーションエラーがあったかをform_insert.phpに持っていく為にセッションを開始
session_start();

// 共通関数の読み込み
require_once "common_function.php";

// ユーザーデータを入れる配列
$user_input_data = [];
$param = $validate_param = [
    "name",
    "post",
    "address",
    "birthday_yy",
    "birthday_mm",
    "birthday_dd",
];

// post送信されたデータを$user_input_dataに詰める
foreach ($param as $p) {
    $user_input_data[$p] = (string) @$_POST[$p];
}
// var_dump($user_input_data);

// バリデートの結果を表すflg
$error_flg = false;

// エラー詳細を詰める配列
$error_detail = [];

// 必須チェック
foreach ($validate_param as $p) {
    if ($user_input_data[$p] === "") {
        $error_flg = true;
        $error_detail["error_must_{$p}"] = true;
    }
}

// 郵便番号の型チェック
if (preg_match("/\A[0-9]{3}[- ]?[0-9]{4}\z/", $user_input_data["post"]) !== 1) {
    $error_flg = true;
    $error_detail["error_format_post"] = true;
}

// 誕生日の型チェック
$int_param = ["birthday_yy", "birthday_mm", "birthday_dd"];
foreach ($int_param as $p) {
    $user_input_data[$p] = (int) $user_input_data[$p];
}

if (
    checkdate(
        $user_input_data["birthday_mm"],
        $user_input_data["birthday_dd"],
        $user_input_data["birthday_yy"]
    ) === false
) {
    $error_detail["error_format_birthday"] = true;
    $error_flg = true;
}
// var_dump($error_flg);
// var_dump($user_input_data);

// CSRFチェック
if (is_csrf_token() === false) {
    $error_detail["error_csrf"] = true;
    $error_flg = true;
}

if ($error_flg) {
    // エラー詳細をセッションに詰める
    $_SESSION["output_buffer"] = $error_detail;
    // 入力内容保持の為にユーザー入力値をセッションに詰める(キーに同じ名前がないことを前提に += で詰める)
    $_SESSION["output_buffer"] += $user_input_data;
    header("Location: ./form_insert.php");
    exit();
}

// DBに繋ぐ
$dbh = get_dbh();
$sql =
    "INSERT INTO test_form (`name`, `post`, `address`, `birthday`, `created`, `updated`) VALUES (:name, :post, :address, :birthday, :created, :updated);";
$pre = $dbh->prepare($sql);

// 値のバインド
$pre->bindValue(":name", $user_input_data["name"], PDO::PARAM_STR);
$pre->bindValue(":post", $user_input_data["post"], PDO::PARAM_STR);
$pre->bindValue(":address", $user_input_data["address"], PDO::PARAM_STR);
// birthdayは年月日で分かれているので連結する
$birthday = "{$user_input_data["birthday_yy"]}-{$user_input_data["birthday_mm"]}-{$user_input_data["birthday_dd"]}";
$pre->bindValue(":birthday", $birthday, PDO::PARAM_STR);
$pre->bindValue(":created", date("Y-m-d h:i:s"), PDO::PARAM_STR);
$pre->bindValue(":updated", date("Y-m-d h:i:s"), PDO::PARAM_STR);

// sqlの実行
$r = $pre->execute();
if ($r === false) {
    echo "システムでエラーが起きました。";
    exit();
}

// insertできたのでセッションを削除する
unset($_SESSION["output_buffer"]);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    入力頂き、ありがとうございます
</body>
</html>
