<?php

namespace Bbs\Controller;

class Login extends \Bbs\Controller
{
  public function run()
  {
    // ログインしていればトップページへ移動
    if ($this->isLoggedIn()) {
      header('Location: ' . SITE_URL);
      exit();
    }
    // 条件：（現状は、紐づいているViewのlogin.phpから）POST送信されたら
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->postProcess();
    }
  }

  // 現状ユーザーが入力した内容は、$_POSTに格納されている
  protected function postProcess()
  {
    // var_dump($_POST);
    // exit();
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('login', $e->getMessage());
    }
    $this->setValues('email', $_POST['email']);
    if ($this->hasError()) {
      return;
    } else {
      try {
        $userModel = new \Bbs\Model\User();
        $user = $userModel->login([
          'email' => $_POST['email'],
          'password' => $_POST['password']
        ]);
      } catch (\Bbs\Exception\UnmatchEmailOrPassword $e) {
        $this->setErrors('login', $e->getMessage());
        return;
      } catch (\Bbs\Exception\DeleteUser $e) {
        $this->setErrors('login', $e->getMessage());
        return;
      }

      // ▼sessionとは
      // ブラウザにユーザーのデータを保存しておくためのもの（Cookie）
      // メリット：ユーザー情報が欲しいときにDBへの接続を行わなくても
      //         sessionにアクセスするだけで良くなる

      // ログイン処理
      //session_regenerate_id関数･･･現在のセッションIDを新しいものと置き換える。セッションハイジャック対策
      session_regenerate_id(true);
      // ユーザー情報をセッションに格納
      // $userには、DBの皆さんそれぞれのアカウントのユーザー情報が格納されている
      // つまりCookieのPHPSESSIDには、ユーザー情報が格納されている
      $_SESSION['me'] = $user;
      // スレッド一覧ページへリダイレクト（=ページへ飛ばす）
      header('Location: ' . SITE_URL . '/thread_all.php');
      exit();
    }
  }
  private function validate()
  {
    // トークンが空またはPOST送信とセッションに格納された値が異なるとエラー
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "トークンが不正です!";
      exit();
    }
    // emailとpasswordのキーがなかった場合、強制終了
    if (!isset($_POST['email']) || !isset($_POST['password'])) {
      echo "不正なフォームから登録されています!";
      exit();
    }
    if ($_POST['email'] === '' || $_POST['password'] === '') {
      throw new \Bbs\Exception\EmptyPost("メールアドレスとパスワードを入力してください!");
    }
  }
}
