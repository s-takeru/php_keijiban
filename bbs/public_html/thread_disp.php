<?php
require_once(__DIR__ . '/header.php');
// コメント作成で使用する
$threadCon = new Bbs\Controller\Thread();
$threadCon->run();
// thread_all.phpから渡ってきたクエリパラメータ（URLに記載される付加情報）
// クエリパラメータを取得するには、$_GETを使用する
$thread_id = $_GET['thread_id'];
// var_dump($thread_id);
// exit();

// ModelのThreadクラスをインスタンス化している
$threadMod = new Bbs\Model\Thread();
// ModelのThreadクラスのgetThreadメソッドを使用している
// getThreadメソッドの引数にクエリパラメータを指定している
// $threadDispには、クエリパラメータに紐づくthreadsテーブルのデータが1件入っている
$threadDisp = $threadMod->getThread($thread_id);

// echo '<pre>';
// var_dump($threadDisp);
// echo '</pre>';
// exit();
?>
<h1 class="page__ttl">スレッド詳細</h1>
<div class="thread">
  <div class="thread__item">
    <div class="thread__head">
      <h2 class="thread__ttl">
        <?= h($threadDisp->title); ?>
      </h2>
    </div>
    <ul class="thread__body">
      <?php
      $comments = $threadMod->getCommentAll($threadDisp->id);
      foreach ($comments as $comment) :
      ?>
        <li class="comment__item">
          <div class="comment__item__head">
            <span class="comment__item__num"><?= h($comment->comment_num); ?></span>
            <span class="comment__item__name">名前：<?= h($comment->username); ?></span>
            <span class="comment__item__date">投稿日時：<?= h($comment->created); ?></span>
          </div>
          <p class="comment__item__content"><?= h($comment->content); ?></p>
        <?php endforeach; ?>
        </li>
    </ul>
    <form action="" method="post" class="form-group">
      <div class="form-group">
        <label>コメント</label>
        <textarea type="text" name="content" class="form-control"><?= isset($threadCon->getValues()->content) ? h($threadCon->getValues()->content) : ''; ?></textarea>
        <p class="err"><?= h($threadCon->getErrors('content')); ?></p>
      </div>
      <div class="form-group">
        <input type="submit" value="書き込み" class="btn btn-primary">
      </div>
      <input type="hidden" name="thread_id" value="<?= h($thread_id); ?>">
      <input type="hidden" name="type" value="createcomment">
      <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
    </form>
    <p class="comment-page thread__date">スレッド作成日時：<?= h($threadDisp->created); ?></p>
  </div>
</div><!-- thread -->
<?php require_once(__DIR__ . '/footer.php'); ?>