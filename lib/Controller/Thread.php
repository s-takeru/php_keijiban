<?php

namespace Bbs\Controller;

class Thread extends \Bbs\Controller
{
  public function run()
  {
    // var_dump($_POST);
    // exit();

    // 条件：（thread_create.phpから）POST送信されていればtrue
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      // （thread_create.phpから）createthreadが送られていればtrue
      // $_POST['name属性']  === 'name属性に対応する値'
      if ($_POST['type']  === 'createthread') {
        $this->createThread();
      // （thread_disp.phpから）createcommentが送られていればtrue
      } elseif ($_POST['type']  === 'createcomment') {
        $this->createComment();
      }
    }
  }

  private function createThread()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('create_thread', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
      $this->setErrors('create_thread', $e->getMessage());
    }
    $this->setValues('thread_name', $_POST['thread_name']);
    $this->setValues('comment', $_POST['comment']);
    if ($this->hasError()) {
      return;
    } else {
      // var_dump($_POST);
      // exit();
      // ViewからControllerに渡ってきたデータは、$_POSTに格納されている
      $threadModel = new \Bbs\Model\Thread();

      // 今度は、ControllerからModelにデータを渡す必要がある
      // $_POST['thread_name']は、ユーザーが入力した部分
      // $_POST['comment']は、ユーザーが入力した部分
      // ユーザーが入力した「thread_name」に「title」という名前をつけている
      // ユーザーが入力した「comment」に「comment」という名前をつけている
      // ユーザー情報が格納されている「sessionのid」に「user_id」という名前をつけている
      // var_dump($_SESSION['me']);
      // exit();
      $threadModel->createThread([
        'title' => $_POST['thread_name'],
        'comment' => $_POST['comment'],
        'user_id' => $_SESSION['me']->id
      ]);
      header('Location: ' . SITE_URL . '/thread_all.php');
      exit();
    }
  }

  // thread_disp.phpから渡ってきたコメントは、$_POSTに格納されている
  private function createComment()
  {
    try {
      $this->validate();
    } catch (\Bbs\Exception\EmptyPost $e) {
      $this->setErrors('content', $e->getMessage());
    } catch (\Bbs\Exception\CharLength $e) {
      $this->setErrors('content', $e->getMessage());
    }
    $this->setValues('content', $_POST['content']);
    if ($this->hasError()) {
      return;
    } else {
      $threadModel = new \Bbs\Model\Thread();
      $threadModel->createComment([
        'thread_id' => $_POST['thread_id'],
        'user_id' => $_SESSION['me']->id,
        'content' => $_POST['content']
      ]);
    }

    // header関数：ページの遷移を行うもの
    header('Location: ' . SITE_URL . '/thread_disp.php?thread_id=' . $_POST['thread_id']);
    exit();
  }

  private function validate()
  {
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
      echo "不正なトークンです!";
      exit();
    }
    if ($_POST['type'] === 'createthread') {
      if (!isset($_POST['thread_name']) || !isset($_POST['comment'])) {
        echo '不正な投稿です';
        exit();
      }
      if ($_POST['thread_name'] === '' || $_POST['comment'] === '') {
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
