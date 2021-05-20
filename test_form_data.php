<?php

// DBを操作するfunction
// ---------------------------------------

function get_test_form($id)
{
    // 最低限のエラーチェック
    if ($id === "") {
        return [];
    }

    // DB接続
    $dbh = get_dbh();
    // sql文
    $sql = "SELECT * FROM test_form WHERE test_form_id = :test_form_id";

    $pre = $dbh->prepare($sql);

    //bind
    $pre->bindValue(":test_form_id", $id, PDO::PARAM_INT);

    $r = $pre->execute();

    if ($r === false) {
        echo "システムにエラーが起きました。";
        exit();
    }

    $data = $pre->fetchAll(PDO::FETCH_ASSOC);

    // 最低限エラーチェック
    if (empty($data)) {
        return [];
        exit();
    }

    $d = $data[0];

    return $d;
}

// 共通バリデーションのfunction
function error_validate($d)
{
    // エラー詳細を詰める配列
    $error_detail = [];

    $validate_param = ["name", "post", "address"];

    // 必須チェック
    foreach ($validate_param as $p) {
        if ($d[$p] === "") {
            $error_detail["error_must_{$p}"] = true;
        }
    }

    // 郵便番号の型チェック
    if (preg_match("/\A[0-9]{3}[- ]?[0-9]{4}\z/", $d["post"]) !== 1) {
        $error_detail["error_format_post"] = true;
    }

    return $error_detail;
}
