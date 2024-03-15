<?php
namespace Bbs\Controller;
class UserUpdate extends \Bbs\Controller {
  public function run() {
    $this->showUser();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $this->updateUser();
    }
  }
  
  protected function showUser() {
    $user = new \Bbs\Model\User();
    $userData = $user->find($_SESSION['me']->id);
    $this->setValues('username', $userData->username);
    $this->setValues('email', $userData->email);
    $this->setValues('image', $userData->image);
  }
  protected function updateUser() {
    try {
      $this->validate();
    } catch (\Bbs\Exception\InvalidEmail $e) {
      $this->setErrors('email', $e->getMessage());
    } catch (\Bbs\Exception\InvalidName $e) {
      $this->setErrors('username', $e->getMessage());
    }
    $this->setValues('username', $_POST['username']);
    $this->setValues('email', $_POST['email']);
    if ($this->hasError()) {
      return;
    } else {
      $user_img = $_FILES['image'];
      $old_img = $_POST['old_image'];
      $ext = substr($user_img['name'], strrpos($user_img['name'], '.') + 1);
      $user_img['name'] = uniqid("img_") .'.'. $ext;
      try {
        $userModel = new \Bbs\Model\User();
        if($user_img['size'] > 0) {
          unlink('./gazou/'.$old_img);
          move_uploaded_file($user_img['tmp_name'],'./gazou/'.$user_img['name']);
          $userModel->update([
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'userimg' => $user_img['name']
          ]);
          $_SESSION['me']->image = $user_img['name'];
        } else {
          $userModel->update([
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'userimg' => $old_img
          ]);
          $_SESSION['me']->image = $old_img;
        }
      }
      catch (\Bbs\Exception\DuplicateEmail $e) {
        $this->setErrors('email', $e->getMessage());
        return;
      }
    }
    $_SESSION['me']->username = $_POST['username'];
    header('Location: '. SITE_URL . '/mypage.php');
    exit();
  }

  private function validate() {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです!";
      exit();
    }
    if (!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
      throw new \Bbs\Exception\InvalidEmail("メールアドレスが不正です!");
    }
    if ($_POST['username'] === '') {
      throw new \Bbs\Exception\InvalidName("ユーザー名が入力されていません!");
    }
  }
}
