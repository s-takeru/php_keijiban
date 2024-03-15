<?php

namespace Bbs\Model;

class User extends \Bbs\Model
{
  // $valuesには、ユーザーが入力したデータが格納されている
  public function create($values)
  {
    // var_dump($values);
    // exit();
    // ユーザーが入力した内容をそのままSQL文に反映させてしまうと、SQLインジェクションという攻撃を受けてしまう
    // 対策：prepareを使用し、すぐに反映させずにワンクッション置く
    $stmt = $this->db->prepare(
      "INSERT INTO users (username,email,password,created,modified)
      VALUES (:username,:email,:password,now(),now())"
      );
    $res = $stmt->execute([
      ':username' => $values['username'],
      ':email' => $values['email'],
      // パスワードのハッシュ化
      // DBにパスワードが格納される際に暗号化される
      ':password' => password_hash($values['password'], PASSWORD_DEFAULT)
    ]);
    // メールアドレスがユニークでなければfalseを返す
    if ($res === false) {
      throw new \Bbs\Exception\DuplicateEmail();
    }
  }



  // 現状ユーザーが入力した内容は、$valuesに格納されている
  public function login($values)
  {
    // var_dump($values);
    // exit();
    // :emailは、ユーザーが入力したemailが格納されている
    $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email;");
    $stmt->execute([
      // $values['email']は、ユーザーが入力したemailが格納されている
      // ':email' => ggggg@gmail.com と同義
      // :emailというのは、SQL文の「WHERE email = :email」の:emailと紐づいている
      // WHERE email = ggggg@gmail.com;" と同義
      ':email' => $values['email']
    ]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, 'stdClass');
    // SQL文の結果が$userに格納されているもしくは、格納されていない
    $user = $stmt->fetch();

    // emptyは、空かどうか判定する（空であればtrue）
    if (empty($user)) {
      throw new \Bbs\Exception\UnmatchEmailOrPassword();
    }

    // password_verifyは、ハッシュ化（暗号化）されているパスワードを判定する
    // 条件：第一引数と第二引数があっていないか
    if (!password_verify($values['password'], $user->password)) {
      throw new \Bbs\Exception\UnmatchEmailOrPassword();
    }

    // $userをloginメソッドの外に出して、再利用できるようにしている
    return $user;
  }
}
