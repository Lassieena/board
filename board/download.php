<?php

// データベースの接続情報
define( 'DB_HOST', 'localhost');
define( 'DB_USER', 'root');
define( 'DB_PASS', '');
define( 'DB_NAME', 'board');

// 変数の初期化
$csv_data = null;
$sql = null;
$res = null;
$message_array = array();
$limit = null;

session_start();

// 取得件数
if( !empty($_GET['limit'])) {
  if( $_GET['limit'] === "10" ) {
    $limit = 10;
  } elseif( $_GET['limit'] === "30" ) {
    $litit = 30;
  }
}

if( !empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {

  //出力の設定
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=メッセージデータ.csv");
  header("Content-Transfer-Encoding: binary");

  // データベースに接続
  $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

  $mysqli->set_charset('utf8');

  // 接続エラーの確認
  if( !$mysqli->connect_errno) {

    if( !empty($litit)) {
      $sql = "SELECT * FROM message ORDER BY post_date ASCLIMIT $limit";
    } else {
      $sql = "SELECT * FROM message ORDER BY post_date ASC";
    }


    // $sql = "SELECT * FROM message ORDER BY post_date ASC";
    $res = $mysqli->query($sql);

    if( $res ) {
      $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }

    $mysqli->close();
  }

  //CSVデータを作成
  if( !empty($message_array) ) {

    //1行目のラベル作成
    $csv_data .= '"ID", ”表示名", "メッセージ", "投稿日時"'."\n";

    foreach( $message_array as $value) {

      // データを1行ずつCSVファイルに書き込む
      $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] . '","' . $value['message'] . '","' . $value['post_date'] . "\"\n";
    }
  }

  // ファイルを出力
  echo $csv_data;

} else {

  header("Location: ./admin.php");
}

return;