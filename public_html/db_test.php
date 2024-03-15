<?php
// 外部ファイルのconfig.phpを読み込んでいる
// config.phpには、DBの接続情報が記載されている
require_once(__DIR__ . '/../config/config.php');

try {
  // config.phpにあるDBの接続情報を元に、DBの接続をPDOオブジェクトで行なっている
  $dbh = new PDO(DSN, DB_USERNAME, DB_PASSWORD);
  // SQL文を記載し、executeで実行している
  $stmt = $dbh->query('SELECT * FROM test');
  $stmt->execute();
  // DBの接続を切断している
  $dbh = null;
  // $stmt->execute();で実行したSQLの実行結果を1件取得してる
  $rec = $stmt->fetch(PDO::FETCH_ASSOC);
  // fetchで取得した内容が$recに格納されているため、その中のname列を出力している
  echo $rec["id"];
} catch (\PDOException $e) {
  echo $e->getMessage();
  exit;
}
