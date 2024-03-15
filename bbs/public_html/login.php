<?php
require_once(__DIR__ . '/header.php');
$app = new Bbs\Controller\Login();
$app->run();
?>
<div class="container">
  <form action="" method="post" id="login" class="form">
    <div class="form-group">
      <label>メールアドレス</label>
      <input type="text" name="email" value="<?= isset($app->getValues()->email) ? h($app->getValues()->email) : ''; ?>" class="form-control">
    </div>
    <div class="form-group">
      <label>パスワード</label>
      <input type="password" name="password" class="form-control">
    </div>
    <p class="err"><?= h($app->getErrors('login')); ?></p>
    <button class="btn btn-primary" onclick="document.getElementById('login').submit();">ログイン</button>
    <input type="hidden" name="token" value="<?= h($_SESSION['token']); ?>">
  </form>
  <p class="fs12"><a href="signup.php">ユーザー登録</a></p>
</div><!--container -->
<?php require_once(__DIR__ . '/footer.php'); ?>