<?php

namespace Bbs\Controller;
// Controllerクラス継承
class Signup extends \Bbs\Controller
{
  public function run()
  {
    if ($this->isLoggedIn()) {
      header('Location: ' . SITE_URL);
      exit();
    }
    // POSTメソッドがリクエストされていればpostProcessメソッド実行
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      // var_dump($_POST);
      // exit();
      $this->postProcess();
    }
  }
  protected function postProcess()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\InvalidEmail $e) {
      $this->setErrors('email', $e->getMessage());
    } catch (\Bbs\Exception\InvalidName $e) {
      $this->setErrors('username', $e->getMessage());
    } catch (\Bbs\Exception\InvalidPassword $e) {
      $this->setErrors('password', $e->getMessage());
    }
    $this->setValues('email', $_POST['email']);
    $this->setValues('username', $_POST['username']);
    if ($this->hasError()) {
      return;
    } else {
      try {
        $userModel = new \Bbs\Model\User();
        // ユーザーが入力したデータは、$_POSTに入っている
        $user = $userModel->create([
          'email' => $_POST['email'],
          'username' => $_POST['username'],
          'password' => $_POST['password']
        ]);
      } catch (\Bbs\Exception\DuplicateEmail $e) {
        $this->setErrors('email', $e->getMessage());
        return;
      }

      // ログイン処理
      $userModel = new \Bbs\Model\User();
      $user = $userModel->login([
        'email' => $_POST['email'],
        'password' => $_POST['password']
      ]);
      session_regenerate_id(true);
      $_SESSION['me'] = $user;
      header('Location: ' . SITE_URL . '/thread_all.php');
      exit();
    }
  }

  // バリデーションメソッド
  private function validate()
  {
    // トークンが空またはPOST送信とセッションに格納された値が異なるとエラー
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです!";
      exit();
    }
    if (!isset($_POST['email']) || !isset($_POST['username']) || !isset($_POST['password'])) {
      echo "不正なフォームから登録されています!";
      exit();
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      throw new \Bbs\Exception\InvalidEmail("メールアドレスが不正です!");
    }
    if ($_POST['username'] === '') {
      throw new \Bbs\Exception\InvalidName("ユーザー名が入力されていません!");
    }
    if (!preg_match('/\A[a-zA-Z0-9]+\z/', $_POST['password'])) {
      throw new \Bbs\Exception\InvalidPassword("パスワードが不正です!");
    }
  }
}
