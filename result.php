<?php

//ポイントデータの初期化
$pointsData = array();
// 的中したポイントと外れたポイント、射撃回数と的中率の値を初期化。
$hitCount = 0;
$missCount = 0;
$totalCount = 0;
$accuracy = 0;


// 別の場所に保存された設定ファイルへのパス
$configFilePath = 'config.php';
// 設定ファイルを読み込む
if (file_exists($configFilePath)) {
  require_once($configFilePath);
} else {
  // 設定ファイルが見つからない場合のエラーハンドリング
  die('設定ファイルが見つかりません。');
}
// データベース接続
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$pdo = new PDO($dsn, $username, $password);


if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
  $start_date = $_POST['start_date'];
  $end_date = $_POST['end_date'];

  // データベースからポイントデータを取得するためのクエリを準備
  $query = "SELECT * FROM `kyudo-shot-table` WHERE selectedDate BETWEEN :start_date AND :end_date";
  $stmt = $pdo->prepare($query);

  // クエリにパラメータをバインド
  $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
  $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);

  // クエリを実行してポイントデータを取得
  $stmt->execute();
  $pointsData = $stmt->fetchAll(PDO::FETCH_ASSOC);



  if (!is_null($pointsData)) {

    // ポイントデータをループ処理して的中率を計算
    foreach ($pointsData as $point) {
      $x = $point['pointX'] - 200;
      $y = $point['pointY'] - 200;

      // x^2 + y^2 <= 200^2 の円内にあるかどうかを判定
      if ($x * $x + $y * $y <= 200 * 200) {
        $hitCount++; // 的中したポイントをカウント
      } else {
        $missCount++; // 外れたポイントをカウント
      }
    }

    // 的中率を計算
    $totalCount = $hitCount + $missCount;
    $accuracy = ($totalCount > 0) ? ($hitCount / $totalCount) * 100 : 0;
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>result</title>

  <link rel="stylesheet" href="resultStyle.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-6">
        <div class="target" id="target" style="display: flex; justify-content: center; align-items: center;">

          <div class="ring ring-0"></div>
          <div class="ring ring-1"></div>
          <div class="ring ring-2"></div>
          <div class="ring ring-3"></div>
          <div class="ring ring-4"></div>
          <div class="ring ring-5"></div>
          <div class="ring ring-6"></div>
          <?php
          // 的中したポイントを赤丸で表示
          foreach ($pointsData as $point) {
            $x = $point['pointX'];
            $y = $point['pointY'];
            echo "<div class='point' style='top: {$y}px; left: {$x}px;'></div>";
          }
          ?>

        </div>
      </div>
    </div>

    <br>
    <br>


    <div class="row justify-content-center">
      <div class="col-6">

        <form method="POST" action="result.php">
          <label for="start_date">開始日：</label>
          <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>">

          <br>

          <label for="end_date">終了日：</label>
          <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>">

          <input type="submit" value="送信">
        </form>


        <table class="table">
          <thead>
            <tr>
              <th>項目</th>
              <th>値</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>射撃回数</td>
              <td><?php echo $totalCount; ?></td>
            </tr>
            <tr>
              <td>的中回数</td>
              <td><?php echo $hitCount; ?></td>
            </tr>
            <tr>
              <td>的中率</td>
              <td><?php echo round($accuracy, 2) . '%'; ?></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <form action="index.html" method="POST">
      <div class="row justify-content-center">
        <div class="col-6">

          <input type="submit" value="トップへ戻る" id="submitButton" name="submitButton">


        </div>
      </div>
    </form>



  </div>

</body>

</html>