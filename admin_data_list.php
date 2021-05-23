<?php

session_start();

require_once "common_function.php";

// セッションのエラー情報を受け取る
$error_detail = [];
if (isset($_SESSION["output_buffer"]["csrf_token"])) {
    $error_detail = $_SESSION["output_buffer"]["csrf_token"];
}
unset($_SESSION["output_buffer"]["csrf_token"]);

// sortのGETデータを受け取る。空の場合はデフォルトとしてtest_form_idを詰める
if (isset($_GET["sort"])) {
    $sort = $_GET["sort"];
} else {
    $sort = "test_form_id";
}

// sortをsql文に流す為のリスト
$sort_list = [
    "test_form_id" => "test_form_id",
    "test_form_id_desc" => "test_form_id DESC",
    "name" => "name",
    "name_desc" => "name DESC",
    "birthday" => "birthday",
    "birthday_desc" => "birthday DESC",
    "created" => "created",
    "created_desc" => "created DESC",
    "updated" => "updated",
    "updated_desc" => "updated DESC",
];

// searchをsql文に流す為のリスト
$search_list = [
    "search_name",
    "search_birthday_start",
    "search_birthday_end",
    "search_created",
];

$search_box = [];
foreach ($search_list as $list) {
    if (isset($_POST[$list]) && !empty($_POST[$list])) {
        $search_box[$list] = $_POST[$list];
        unset($_POST[$list]);
    }
}

// WHERE name = "test"

// DB接続
$dbh = get_dbh();

// sql文
$sql = "SELECT * FROM test_form";

// searchをsqlに追加
$sql_arr = $bind_arr = [];
if (!empty($search_box)) {
    if (
        isset($search_box["search_name"]) &&
        !empty($search_box["search_name"])
    ) {
        $sql_arr[] = " name = :name";
        $bind_arr[":name"] = $search_box["search_name"];
    }
    if (
        isset($search_box["search_birthday_start"]) &&
        !empty($search_box["search_birthday_start"])
    ) {
        $sql_arr[] = " birthday >= :birthday_start";
        $bind_arr[":birthday_start"] = $search_box["search_birthday_start"];
    }
    if (
        isset($search_box["search_birthday_end"]) &&
        !empty($search_box["search_birthday_end"])
    ) {
        $sql_arr[] = " birthday <= :birthday_end";
        $bind_arr[":birthday_end"] = $search_box["search_birthday_end"];
    }
    if (
        isset($search_box["search_created"]) &&
        !empty($search_box["search_created"])
    ) {
        $sql_arr[] = " created >= :created_start";
        $bind_arr[":created_start"] =
            $search_box["search_created"] . " 00:00:00";
    }
    if (
        isset($search_box["search_created"]) &&
        !empty($search_box["search_created"])
    ) {
        $sql_arr[] = " created <= :created_end";
        $bind_arr[":created_end"] = $search_box["search_created"] . " 23:59:59";
    }

    $sql .= " WHERE " . implode(" AND", $sql_arr);
}

// sortをsqlに追加
$sql .= " ORDER BY " . $sort_list[$sort] . ";";

$pre = $dbh->prepare($sql);

// searchの為のbind
foreach ($bind_arr as $key => $val) {
    $pre->bindValue($key, $val);
}

$r = $pre->execute();

if ($r === false) {
    echo "システムにエラーが発生しました。";
    exit();
}

$data = $pre->fetchAll(PDO::FETCH_ASSOC);

// データ削除用のトークンを発行
$csrf_token = create_csrf_token_admin();

// sortのマークを切り替える関数
function change_mark($type, $mark)
{
    $str = "";
    if ($GLOBALS["sort"] === $type) {
        $str = "<a href='./admin_data_list.php?sort={$type}' class='text-danger'>{$mark}</a>";
    } else {
        $str = "<a href='./admin_data_list.php?sort={$type}' class='text-muted'>{$mark}</a>";
    }
    return $str;
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
  <style type="text/css">
  .search_text {margin: 10px 0;}
  </style>
</head>
<body>
  <div class="container">
  <h4 style="margin-top: 50px; margin-bottom: 20px;">フォーム内容一覧</h4>
  <?php if (!empty($error_detail)): ?>
  <span class="text-danger">CSRFトークンでエラーが起きました。正しい遷移を5分以内にして下さい。</span>
  <?php endif; ?>

  <h5 style="margin: 20px 0;">検索</h5>
  <form action="./admin_data_list.php" method="POST">
    検索する「名前」
    <input type="text" class="search_text" name="search_name" 
    value="<?php echo h(@$search_box["search_name"]); ?>"><br>
    検索する「誕生日(YYYY-MM-DD)」
    <input type="text" class="search_text" name="search_birthday_start" 
    value="<?php echo h(@$search_box["search_birthday_start"]); ?>"> ~ 
    <input type="text" name="search_birthday_end" 
    value="<?php echo h(@$search_box["search_birthday_end"]); ?>"><br>
    検索する「入力日(YYYY-MM-DD)」
    <input type="text" class="search_text" name="search_created" 
    value="<?php echo h(@$search_box["search_created"]); ?>"><br>
    <button type="submit" class="btn btn-warning">検索</button>
  </form>

  <h5 style="margin: 30px 0;">一覧</h5>
    <table class="table table-hover">
      <tr>
        <th>ID</th>
        <th>名前</th>
        <th>誕生日</th>
        <th>入力日</th>
        <th>更新日 </th>
      </tr>
      <tr>
        <td><?php echo change_mark(
            "test_form_id",
            "▲"
        ); ?> <?php echo change_mark("test_form_id_desc", "▼"); ?></td>
        <td><?php echo change_mark("name", "▲"); ?> <?php echo change_mark(
     "name_desc",
     "▼"
 ); ?></td>
      <td><?php echo change_mark("birthday", "▲"); ?> <?php echo change_mark(
     "birthday_desc",
     "▼"
 ); ?></td>
      <td><?php echo change_mark("created", "▲"); ?> <?php echo change_mark(
     "created_desc",
     "▼"
 ); ?></td>
        <td><?php echo change_mark("updated", "▲"); ?> <?php echo change_mark(
     "updated_desc",
     "▼"
 ); ?></td>
      </tr>
      <?php foreach ($data as $d): ?>
        <tr>
          <td><?php echo h($d["test_form_id"]); ?></td>
          <td><?php echo h($d["name"]); ?></td>
          <td><?php echo h($d["birthday"]); ?></td>
          <td><?php echo h($d["created"]); ?></td>
          <td><?php echo h($d["updated"]); ?></td>
          <td><a class="btn btn-primary" href="./admin_data_detail.php?test_form_id=<?php echo rawurldecode(
              $d["test_form_id"]
          ); ?>">詳細</a></td>
          <td><a class="btn btn-primary" href="./admin_data_update.php?test_form_id=<?php echo rawurldecode(
              $d["test_form_id"]
          ); ?>">修正</a></td>
          <form action="./admin_data_delete.php" method="POST">
            <input type="hidden" name="test_form_id" value="<?php echo h(
                $d["test_form_id"]
            ); ?>">
            <input type="hidden" name="csrf_token" value="<?php echo h(
                $csrf_token
            ); ?>">
            <td><button type="submit" class="btn btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button></td>
          </form>
        </tr>
    <?php endforeach; ?>
    </table>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
