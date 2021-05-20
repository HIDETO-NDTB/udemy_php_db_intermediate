<?php
// 共通の関数
// ---------------------------------------

// XSS(クロスサイトスクリプティング)を防ぐ為の関数
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}

// 0だった場合は空文字を返す
function h_digit($d)
{
    if ($d === 0) {
        return "";
    } else {
        return h((string) $d);
    }
}

// CSRF(クロスサイトリクエストフォージェリ)用の共通関数
// ---------------------------------------
// トークンの作成

// front用
function create_csrf_token()
{
    return _create_csrf_token("front");
}

// 管理画面用
function create_csrf_token_admin()
{
    return _create_csrf_token("admin");
}

function _create_csrf_token($type)
{
    $csrf_token = "";
    try {
        if (function_exists("random_bytes")) {
            $csrf_token = hash("sha512", random_bytes(128));
        } elseif (is_readable("/dev/urandom")) {
            $csrf_token = hash(
                "sha512",
                file_get_contents("/dev/urandom", false, null, 0, 128),
                false
            );
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $csrf_token = hash("sha512", openssl_random_pseudo_bytes(128));
        }
    } catch (Exception $e) {
    }
    if ($csrf_token === "") {
        echo "CSRFトークンが作成できないので終了します";
        exit();
    }

    // トークンを5個までに制御
    if (isset($_SESSION[$type]["csrf_token"])) {
        while (count(@$_SESSION[$type]["csrf_token"]) >= 5) {
            array_shift($_SESSION[$type]["csrf_token"]);
        }
    }

    // セッションに格納
    $_SESSION[$type]["csrf_token"][$csrf_token] = time();

    return $csrf_token;
}

// フロント用
function is_csrf_token()
{
    return _is_csrf_token("front");
}

// 管理画面用
function is_csrf_token_admin()
{
    return _is_csrf_token("admin");
}

// トークンの確認
function _is_csrf_token($type)
{
    $post_csrf_token = (string) @$_POST["csrf_token"];

    // セッションの中にPOST送信されたトークンがなければfalse
    if (!isset($_SESSION[$type]["csrf_token"][$post_csrf_token])) {
        return false;
    }

    // 寿命の把握
    $ttl = $_SESSION[$type]["csrf_token"][$post_csrf_token];
    // トークンの作成時間を削除
    unset($_SESSION[$type]["csrf_token"][$post_csrf_token]);
    // 寿命チェック(5分以内)
    if ($ttl + 300 <= time()) {
        return false;
    }

    // 全てのチェックがOKなので・・・
    return true;
}

// DB用関数
// ---------------------------------------
function get_dbh()
{
    $user = "root";
    $pass = "root";
    $dsn = "mysql:dbname=udemy_php_intermediate;host=localhost;charset=utf8mb4";

    // 接続オプションの設定
    $opt = [
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // 複文禁止が可能なら付け足しておく
    if (defined("PDO::MYSQL_ATTR_MULTI_STATEMENTS")) {
        $opt[PDO::MYSQL_ATTR_MULTI_STATEMENTS] = false;
    }

    // 接続
    try {
        $dbh = new PDO($dsn, $user, $pass, $opt);
    } catch (PDOException $e) {
        echo "システムでエラーが起きました";
        exit();
    }
    return $dbh;
}
