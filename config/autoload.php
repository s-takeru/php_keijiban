<?php
// プログラム上で未定義のクラスが見つかったら spl_autoload_register で定義した内容に従って自動的にファイルを require する
spl_autoload_register(function ($class) {
  $prefix = 'Bbs\\';
  if (strpos($class, $prefix) === 0) {
    $className = substr($class, strlen($prefix));
    $classFilePath = __DIR__ . '/../lib/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($classFilePath)) {
      require $classFilePath;
    }
  }
});
