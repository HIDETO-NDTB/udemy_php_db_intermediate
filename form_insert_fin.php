<?php

// HTTP responseヘッダを出力する可能性があるので、バッファリングしておく
ob_start();

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

// 必須チェック
foreach ($validate_param as $p) {
    if ($user_input_data[$p] === "") {
        $error_flg = true;
    }
}

// 郵便番号の型チェック
if (preg_match("/\A[0-9]{3}[- ]?[0-9]{4}\z/", $user_input_data["post"]) !== 1) {
    $error_flg = true;
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
    $error_flg = true;
}
// var_dump($error_flg);
// var_dump($user_input_data);

if ($error_flg) {
    header("Location: ./form_insert.php");
    exit();
}

echo "OK";
