<?php
ini_set('display_errors', 1);
define('DSN', 'mysql:host=localhost;charset=utf8;dbname=bbs');
define('DB_USERNAME', 'bbs_user');
define('DB_PASSWORD', 'T6Z!0GG@nIK8pSJx');
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/bbs/public_html');
require_once(__DIR__ . '/../lib/Controller/functions.php');
require_once(__DIR__ . '/autoload.php');
session_start();
