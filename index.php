<?php
date_default_timezone_set("Asia/Tokyo");

// クライアントからのAjaxリクエストを受け取る
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // 受け取ったデータを取得
  $jsonData = file_get_contents('php://input');

  // 受け取ったデータをデコード
  $data = json_decode($jsonData, true);

  $file = fopen('received_data.txt', 'w');
  fwrite($file, print_r($data, true));
  fclose($file);


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


  if (isset($data) && !empty($data)) {
    $pdo->beginTransaction();

    foreach ($data as $item) {
      $selectedDate = $item['selectedDate'];
      $postDate =  date("Y-m-d H:i:s"); // 日付の形式に合わせて値を設定する
      $shotCount = $item['shotCount'];
      $pointX = $item['x'];
      $pointY = $item['y'];
      $pointNumber = $item['pointNumber'];

      $query = "INSERT INTO `kyudo-shot-table`(`shotCount`, `pointNumber`, `pointX`, `pointY`, `selectedDate`, `postDate`) VALUES (:shotCount, :pointNumber, :pointX, :pointY, :selectedDate, :postDate)";
      $stmt = $pdo->prepare($query);
      $stmt->bindParam(":selectedDate", $selectedDate);
      $stmt->bindParam(":postDate", $postDate);
      $stmt->bindParam(":shotCount", $shotCount);
      $stmt->bindParam(":pointX", $pointX);
      $stmt->bindParam(":pointY", $pointY);
      $stmt->bindParam(":pointNumber", $pointNumber);
      $stmt->execute();

      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    $pdo->commit();
  }

  // データベース接続を閉じる
  $pdo = null;
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Archery Target</title>

  <link rel="stylesheet" href="style.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>

<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-6">
        <div class="target" id="target">
          <div class="ring ring-0"></div>
          <div class="ring ring-1"></div>
          <div class="ring ring-2"></div>
          <div class="ring ring-3"></div>
          <div class="ring ring-4"></div>
          <div class="ring ring-5"></div>
          <div class="ring ring-6"></div>
        </div>
      </div>
    </div>

    <br>
    <form action="index.php" method="POST">



      <div class="row justify-content-center">
        <div class="col-6">
          <div class="shot-count">
            射撃回数:
            <select id="shotCountSelect" name="shotCount">

              <?php
              for ($i = 0; $i <= 10; $i++) {
                echo "<option value='$i'>$i</option>";
              }
              ?>
            </select>

            <input type="submit" value="送信" id="submitButton" name="submitButton">



            <div class="input-daterange input-group" id="datepicker">
              <input type="date" class="form-control" id="startDate" name="selectedDate">
            </div>

          </div>
        </div>
      </div>
    </form>
  </div>





  <script src="script.js"></script>

  <!-- jQuery, popper.js, Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

  <!-- bootstrap-datepicker -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.ja.min.js"></script>


</body>

</html>