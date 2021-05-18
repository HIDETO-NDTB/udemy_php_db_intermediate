<?php

require_once "common_function.php";

// DB接続
$dbh = get_dbh();

// sql文
$sql = "SELECT * FROM test_form";

$pre = $dbh->prepare($sql);

// bindはなし

$r = $pre->execute();

if ($r === false) {
    echo "システムにエラーが発生しました。";
    exit();
}

$data = $pre->fetchAll(PDO::FETCH_ASSOC);

// var_dump($data);
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
  <h4 style="margin-top: 50px; margin-bottom: 20px;">フォーム内容一覧</h4>
    <table class="table table-hover">
      <?php foreach ($data as $d): ?>
        <tr>
          <td><?php echo h($d["test_form_id"]); ?></td>
          <td><?php echo h($d["name"]); ?></td>
          <td><?php echo h($d["created"]); ?></td>
          <td><?php echo h($d["updated"]); ?></td>
          <td><a class="btn btn-primary" href="./admin_data_detail.php?test_form_id=<?php echo rawurldecode(
              $d["test_form_id"]
          ); ?>">一覧</a></td>
        </tr>
    <?php endforeach; ?>
    </table>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
