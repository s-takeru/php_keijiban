<?php

namespace Bbs\Controller;

class Logout extends \Bbs\Controller
{
  public function run()
  {
    // 条件：（header.phpから）POST送信されていればtrue
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // 条件①：name属性tokenが送信されていなければtrue
      // 条件②：送信されたtokenとsessionのtokenが正しくなければtrue
      // 上記のどちらかがtrueになれば、全体評価はtrueになる
      if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        echo "不正なトークンです!";
        exit();
      }

      $_SESSION = [];
      if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 86400, '/');
      }
      // セッションの破棄
      session_destroy();
    }
    // トップページへリダイレクト
    header('Location: ' . SITE_URL);
  }
}
