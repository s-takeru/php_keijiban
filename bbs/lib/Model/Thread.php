<?php

namespace Bbs\Model;

class Thread extends \Bbs\Model
{
   // ユーザーが入力した内容は、$valuesに格納されている
  public function createThread($values)
  {
    // var_dump($values);
    // exit();

    // todo:3/8は以下から解説をする
    try {
      $this->db->beginTransaction();
      // threadsテーブルの処理
      $sql =
      "INSERT INTO threads (user_id,title,created,modified)
      VALUES (:user_id,:title,now(),now())"
      ;
      // prepareで、SQLの実行準備を行なっている
      $stmt = $this->db->prepare($sql);
      // bindValueで、$sqlのSQL文とユーザーが入力したものを紐づけている
      // $stmt->bindValue('user_id', 1);
      // $stmt->bindValue('title', teretete);
      $stmt->bindValue('user_id', $values['user_id']);
      $stmt->bindValue('title', $values['title']);
      // executeで、SQL文の実行を行なっている
      $res = $stmt->execute();

      // ここからは、commentsテーブルの処理
      // lastInsertIdは、最後に登録したデータのIDを取得する
      // 今回は、上で記述したthreadsテーブルのIDが対象となる
      $thread_id = $this->db->lastInsertId();
      $sql =
        "INSERT INTO comments (thread_id,comment_num,user_id,content,created,modified)
        VALUES (:thread_id,1,:user_id,:content,now(),now())"
      ;
      $stmt = $this->db->prepare($sql);
      $stmt->bindValue('thread_id', $thread_id);
      $stmt->bindValue('user_id', $values['user_id']);
      $stmt->bindValue('content', $values['comment']);
      $res = $stmt->execute();
      $this->db->commit();
    } catch (\Exception $e) {
      echo $e->getMessage();
      $this->db->rollBack();
    }
  }

  // 全スレッド取得
  public function getThreadAll()
  {
    // delflagが0のものだけを取得するので、delflagが1のものは取得しない
    $stmt = $this->db->query(
      "SELECT id,title,created FROM threads WHERE delflag = 0 ORDER BY id desc"
    );
    // 今まで使用してきたfetchは、1件しかデータを取得できない
    // 今回のfetchAllは、複数件のデータを取得できる
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // コメント取得
  // threadsテーブルののid列と紐づいているコメントを取得する（複数件）
  // $thread_idは、Viewから渡ってきたthreadsテーブルのid列が格納されている
  public function getComment($thread_id)
  {
    // 「SELECT comment_num,username,content」は、commentsテーブルの列
    // 「SELECT comments.created」もcommentsテーブルの列
    // 「INNER JOIN users」は、commentsテーブルとusersテーブルが結合してる
    // 「ON user_id = users.id」は、
    // commentsテーブルのuser_idとusersテーブルのidが等しいものがある場合結合する
    // WHERE thread_id =:thread_id AND comments.delflag = 0は、
    // commentsテーブルのthread_idとViewから渡ってきたthread_idが等しいものかつ
    // commentsテーブルのdelflagが0のものを取得する
    // 「ORDER BY comment_num ASC」は、
    // commentsテーブルのcomment_numの数字が小さい順に取得する
    // 「LIMIT 5」は、取得するデータを5件までにする

    $stmt = $this->db->prepare(
      "SELECT comment_num,username,content,comments.created FROM comments
      INNER JOIN users ON user_id = users.id
      WHERE thread_id =:thread_id AND comments.delflag = 0
      ORDER BY comment_num ASC LIMIT 5;"
    );

    // WHERE thread_id =:thread_id
    // commentsテーブルのthread_id列とViewから渡ってきた引数のthread_idを紐付けている
    $stmt->execute([':thread_id' => $thread_id]);
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // コメント数取得
  // $thread_idは、Viewから渡ってきたthreadsテーブルのid列が格納されている
  public function getCommentCount($thread_id)
  {
    // WHERE thread_id =:thread_idは、
    // commentsテーブルのthread_idとViewから渡ってきたthread_idが等しいもの
    // 引数$thread_idに紐づくコメントの数は、record_numに入っている
    $stmt = $this->db->prepare(
      "SELECT COUNT(comment_num) AS record_num FROM comments
      WHERE thread_id = :thread_id AND delflag = 0;"
    );
    $stmt->bindValue('thread_id', $thread_id);
    $stmt->execute();

    // SQLの実行結果は、$resに入っている
    $res =  $stmt->fetch(\PDO::FETCH_ASSOC);
    return $res['record_num'];
  }





  // 渡ってきたクエリパラメータに紐づくスレッドを1件取得
  // 引数$thread_idには、クエリパラメータの情報が入っている
  public function getThread($thread_id)
  {
    // var_dump($thread_id);
    // exit();

    // ▼WHERE id = :id
    // threadsテーブルのid列とthread_disp.phpから渡ってきたクエリパラメータが等しいもの
    $stmt = $this->db->prepare(
      "SELECT * FROM threads
      WHERE id = :id AND delflag = 0;"
    );

    // SQLの:idとthread_disp.phpから渡ってきたクエリパラメータを紐付けている
    $stmt->bindValue(":id", $thread_id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_OBJ);
  }

  // 渡ってきたスレッドのクエリパラメータに紐づくコメントを全件取得
  public function getCommentAll($thread_id)
  {
    // commentsテーブルとusersテーブルが結合している
    // commentsテーブル：comment_num,content,created
    // usersテーブル：username
    // 結合の条件：commentsテーブルのuser_idとusersテーブルのidが正しいもの
    // WHERE：thread_id =:thread_id
    // ->commentsテーブルのthread_idとクエリパラメータのthread_idが紐づくもの
    $stmt = $this->db->prepare(
      "SELECT comment_num,username,content,comments.created FROM comments
      INNER JOIN users ON user_id = users.id
      WHERE thread_id = :thread_id AND comments.delflag = 0
      ORDER BY comment_num ASC;
      ");
    $stmt->execute([':thread_id' => $thread_id]);
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }

  // コメント投稿
  // ControllerのThread.phpから渡ってきたデータは、引数の$valuesに入っている
  public function createComment($values)
  {
    // var_dump($values);
    // exit();
    try {
      $this->db->beginTransaction();
      $lastNum = 0;
      // ORDER BY comment_num DESC LIMIT 1は、
      // comment_numの値が一番大きいものを1件だけ取得する
      $sql =
      "SELECT comment_num FROM comments
      WHERE thread_id = :thread_id ORDER BY comment_num DESC LIMIT 1";
      $stmt = $this->db->prepare($sql);

      $stmt->bindValue('thread_id', $values['thread_id']);
      $stmt->execute();

      // $resに取得したデータを格納している
      $res = $stmt->fetch(\PDO::FETCH_OBJ);

      // $lastNumには、数字入っている（3など）
      $lastNum = $res->comment_num;

      // comment_numの数字が重複してしまうので、+1して重複しないようにしている
      $lastNum++;

      $sql =
      "INSERT INTO comments (thread_id,comment_num,user_id,content,created,modified)
      VALUES (:thread_id,:comment_num,:user_id,:content,now(),now())";
      $stmt = $this->db->prepare($sql);

      // $values['thread_id']は、クエリパラメータのthread_id
      // $lastNumは、SELECT文で取得したものが5であれば、+1されるので、6になる
      // $values['user_id']は、$_SESSION。つまりログインしているユーザーのid
      // $values['content']は、ユーザーが入力したもの
      $stmt->bindValue('thread_id', $values['thread_id']);
      $stmt->bindValue('comment_num', $lastNum);
      $stmt->bindValue('user_id', $values['user_id']);
      $stmt->bindValue('content', $values['content']);
      $stmt->execute();
      $this->db->commit();
    } catch (\Exception $e) {
      echo $e->getMessage();
      $this->db->rollBack();
    }
  }

}


