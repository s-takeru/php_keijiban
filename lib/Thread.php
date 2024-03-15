<?php
namespace Bbs\Controller;
class Thread extends \Bbs\Controller {
  public function run() {
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
      if ($_POST['type']  === 'createthread') {
        $this->createThread();
      }
    }
  }

  private function createThread(){
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
        $this->setErrors('create_thread', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
        $this->setErrors('create_thread', $e->getMessage());
    }
    $this->setValues('thread_name', $_POST['thread_name']);
    $this->setValues('comment', $_POST['comment']);
    if ($this->hasError()){
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadModel->createThread([
        'title' => $_POST['thread_name'],
        'comment' => $_POST['comment'],
        'user_id' => $_SESSION['me']->id
      ]);
      header('Location: '. SITE_URL . '/thread_all.php');
      exit();
    }
  }

  private function validate() {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです!";
      exit();
    }
    if ($_POST['type'] === 'createthread') {
      if (!isset($_POST['thread_name']) || !isset($_POST['comment'])){
        echo '不正な投稿です';
        exit();
      }
      if ($_POST['thread_name'] === '' || $_POST['comment'] === ''){
        throw new \Bbs\Exception\EmptyPost("スレッド名または最初のコメントが入力されていません！");
      }
      if (mb_strlen($_POST['thread_name']) > 20) {
        throw new \Bbs\Exception\CharLength("スレッド名が長すぎます！");
      }
      if (mb_strlen($_POST['comment']) > 200) {
        throw new \Bbs\Exception\CharLength("コメントが長すぎます！");
      }
    }
  }
}