<?php

ob_start();
session_start();

require_once "common_function.php";
require_once "test_form_data.php";

$test_form_id = (string) @$_GET["test_form_id"];

if ($test_form_id === "") {
    header("Location: ./admin_data_list.php");
    exit();
}

$d = get_test_form($test_form_id);

if (empty($d)) {
    header("Location: ./admin_data_list.php");
    exit();
}

if (isset($_SESSION["output_buffer"])) {
    $d = $_SESSION["output_buffer"] + $d;
}
unset($_SESSION["output_buffer"]);

$csrf_token = create_csrf_token_admin();
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
  <?php if (isset($d["error_token"]) && $d["error_token"] === true): ?>
    <span class="text-danger">CSRFトークンでエラーが起きました。正しい遷移を5分以内にして下さい。</span>
  <?php endif; ?>
  <h4 style="margin-top: 50px; margin-bottom: 20px;">フォーム内容修正</h4>
    <table class="table table-hover">
      <form action="./form_data_update_fin.php" method="post">
      <input type="hidden" name="csrf_token" value="<?php echo h(
          $csrf_token
      ); ?>">
      <input type="hidden" name="test_form_id" value="<?php echo h(
          $d["test_form_id"]
      ); ?>">
      <tr>
        <?php if (
            isset($d["error_must_name"]) &&
            $d["error_must_name"] === true
        ): ?>
          <span class="text-danger">名前が未入力です<br></span>
        <?php endif; ?>
        <td>名前</td>
        <td><input type="text" name="name" value="<?php echo h(
            $d["name"]
        ); ?>"></td>
      </tr>
      <tr>
        <?php if (
            isset($d["error_must_post"]) &&
            $d["error_must_post"] === true
        ): ?>
          <span class="text-danger">郵便番号が未入力です<br></span>
        <?php endif; ?>
        <?php if (
            isset($d["error_format_post"]) &&
            $d["error_format_post"] === true
        ): ?>
          <span class="text-danger">郵便番号の形式が違います<br></span>
        <?php endif; ?>
        <td>郵便番号(例: 999-9999)</td>
        <td><input type="text" name="post" value="<?php echo h(
            $d["post"]
        ); ?>"></td>
      </tr>
      <tr>
        <?php if (
            isset($d["error_must_address"]) &&
            $d["error_must_address"] === true
        ): ?>
          <span class="text-danger">住所が未入力です<br></span>
        <?php endif; ?>
        <td>住所</td>
        <td><input type="text" name="address" value="<?php echo h(
            $d["address"]
        ); ?>"></td>
      </tr>
      <tr>
        <?php if (
            isset($d["error_must_birthday"]) &&
            $d["error_must_birthday"] === true
        ): ?>
          <span class="text-danger">誕生日が未入力です<br></span>
        <?php endif; ?>
        <?php if (
            isset($d["error_format_birthday"]) &&
            $d["error_format_birthday"] === true
        ): ?>
          <span class="text-danger">誕生日の形式が違います<br></span>
        <?php endif; ?>
        <td>誕生日</td>
        <td><input type="text" name="birthday" value="<?php echo h(
            $d["birthday"]
        ); ?>"></td>
      </tr>
      <tr>
        <td><button type="submit" class="btn btn-primary">データ修正</button></td>
      </tr>
      </form>
    </table>
  </div>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>