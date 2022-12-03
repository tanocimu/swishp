<?php
/**
 * パス設定
 */
//アプリケーションディレクトリ
define('DIR_PATH', dirname(__FILE__).'/');
//モデルディレクトリ
define('DIR_LOGIN', DIR_PATH.'login/');
//ビューディレクトリ
define('DIR_IMAGES', DIR_PATH.'images/');
//ライブラリディレクトリ
define('DIR_STOCKIMAGES', DIR_PATH.'stock_images/');

/**
 * データベース設定
 */
//データベースの種類
define('DB_STORAGE', 'mysql');
//データベースのホスト名
define('DB_HOSTNAME', 'localhost');
//データベース名
define('DB_DATABASE', 'mtts1');
//データベースユーザー名
define('DB_USERNAME', 'mtts1');
//データベースパスワード
define('DB_PASSWORD', 'Jungfrau12');
//データベース文字コード設定
define('DB_CHARSET', false);
//プレフィックス
define('DB_PREFIX', 'mtts_');
?>