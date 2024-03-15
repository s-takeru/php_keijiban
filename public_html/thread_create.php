<?php
require_once(__DIR__ . '/header.php');
$app = new Bbs\Controller\Thread();
$app->run();
?>
<h1 class="page__ttl">新規スレッド</h1>
<form action="" method="post" class="form-group new_thread" id="new_thread">
  <div class="form-group">

    <label>スレッド名</label>
    <input type="text" name="thread_name" class="form-control" value="<?= isset($app->getValues()->thread_name) ? h($app->getValues()->thread_name) : ''; ?>">

  </div>
  <div class="form-group">

    <label>最初のコメント</label>
    <textarea type="text" name="comment" class="form-control"><?= isset($app->getValues()->comment) ? h($app->getValues()->comment) : ''; ?></textarea>

    <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">

    <!-- ▼ControllerのThreadクラスと連結してる -->
    <input type="hidden" name="type" value="createthread">
    <!-- ▲ControllerのThreadクラスと連結してる -->

    <p class="err"><?= h($app->getErrors('create_thread')); ?></p>
  </div>
  <div class="form-group btn btn-primary" onclick="document.getElementById('new_thread').submit();">作成</div>
</form>
<?php
require_once(__DIR__ . '/footer.php');
?>