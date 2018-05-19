<?php
//=====================================
//
// 共通定数
//
//=====================================
define('COPYRIGHT', '©2016 Atelier UEDA🐢');

//=====================================
//
// 共通クローズ処理
//
//=====================================

//=====================================
//
// 共通ルーチン
//
//=====================================
//---------------------------------------------------------------
// スクリプト入り口ログの出力
// 
// スクリプトファイルの入り口で、ファイル日付等のログを残す
//---------------------------------------------------------------
function us_script_entry_log(){
	// ファイルの更新日時
	$updated = date( "Y/m/d H:i", getlastmod() );
	error_log("us_log:"."script_entry ".__FILE__." ".$updated);
}

//---------------------------------------------------------------
// エラーログの出力
// 
// 1. session の有効範囲の設定
// 2. time zone の設定（ないと latest な PHP でワーニングがでる）
//---------------------------------------------------------------
function us_error_log(){
}

//---------------------------------------------------------------
// ブラウザ設定言語の設定
//---------------------------------------------------------------
// lang code: http://www.futomi.com/lecture/env_var/http_accept_language.html
function cmn_check_lang(){
	if (isset($_GET['lang'])){
    $lang = $_GET['lang'];
  } elseif (isset($_POST['lang'])){
    $lang = $_POST['lang'];
  } else {
    // ブラウザの言語設定
    // https://qiita.com/Sankame/items/ceaaf07c7d870e5e5248
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    switch($lang){
      case "en": // U.K. English
      case "ja": // Japanese
//      case "zh": // Chinese
        break;
      default:
        $lang = "en";
        break;
    }
  }
  return $lang;
}
?>


<?php function show_html_head($title) { ?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
	<title><?= $title ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="jquery.mobile-1.3.2.min.css" />
	<script src="jquery-1.11.3.min.js"></script>
  <script src="/SCRIPT/gc_common/resource/custom-scripting.js"></script>
	<script src="jquery.mobile-1.3.2.min.js"></script>
	<link href="../fontawesome/web-fonts-with-css/css/fontawesome-all.min.css" rel="stylesheet">
</head>

<?php } ?>

<?php function show_html_jquery_footer() { ?>

<div data-role="footer" data-position="fixed"  class="no-cache">
    <h4><?php echo COPYRIGHT ?></h4>
</div>

<?php } ?>