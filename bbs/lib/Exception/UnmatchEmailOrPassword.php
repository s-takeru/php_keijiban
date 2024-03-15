<?php

namespace Bbs\Exception;

class UnmatchEmailOrPassword extends \Exception
{
  protected $message = 'メールアドレスまたはパスワードが一致しません。';
}
