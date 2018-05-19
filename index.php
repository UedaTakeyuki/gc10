<?php
//===============================================================
// 共通処理
//===============================================================
require_once("common.php");
//require_once("../gc_common/common.php");
require_once("wordsdef.php");
error_log("us_log:"."script_entry ".__FILE__." updated at ".date( "Y/m/d H:i", getlastmod())); // ファイルヘッダログ

//===============================================================
// 定数
//===============================================================
define("TITLE","Finder");

// ファイル
/* 選択デバイスを GET パラメタで持つのが良いか、tmp ファイルに持つのが良いか動作の安定性を鑑み要検討
define("PATH", "/tmp/");
define("LOCK_FN","adjust_fromUVC_locked.txt");
define("LOCK_PATH_FN",PATH . LOCK_FN);
*/

// シェルスクリプト
//define("MJPG_STREAMER", 'export LD_PRELOAD=::/usr/lib/uv4l/uv4lext/armv6l/libuv4lext.so; /home/pi/MCC/mjpg/mjpg-streamer/mjpg_streamer -i "/home/pi/MCC/mjpg/mjpg-streamer/input_uvc.so -d /dev/video0 -f 5 -r 160x120 -y" -o "/home/pi/MCC/mjpg/mjpg-streamer/output_http.so -w /tmp -p 9000" >/dev/null 2>&1 &');
define("MJPG_STREAMER", 'sudo /home/pi/install/mjpg-streamer/mjpg_streamer -i "/home/pi/install/mjpg-streamer/input_uvc.so -d /dev/video0 -f 5 -r 160x120 -y" -o "/home/pi/install/mjpg-streamer/output_http.so -w /tmp -p 9000" >/dev/null 2>&1 &');
define("MJPG_STREAMER1", 'sudo /home/pi/install/mjpg-streamer/mjpg_streamer -i "/home/pi/install/mjpg-streamer/input_uvc.so -d ');
//define("MJPG_STREAMER2", ' -f 5 -r 160x120 -y" -o "/home/pi/install/mjpg-streamer/output_http.so -w /tmp -p 9000" >/dev/null 2>&1 &');
define("MJPG_STREAMER2", ' -f 5 -r ');
define("MJPG_STREAMER3", ' -y" -o "/home/pi/install/mjpg-streamer/output_http.so -w /tmp -p 9000" >/dev/null 2>&1 &');
define("KILLMJPG_STREAMER", "sudo pkill -9 -f mjpg >/dev/null 2>&1");

// 言語設定
// lang code: http://www.futomi.com/lecture/env_var/http_accept_language.html
$lang = cmn_check_lang();

// video デバイスの一覧を取得
$video_devices = glob("/dev/video*");

$on = 0;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// 1. stop previous mjpg
	$output=shell_exec(KILLMJPG_STREAMER);
	#sleep(1);

	// 2. start this mgpg
	$command = MJPG_STREAMER1 . $_POST["selected_device"] .MJPG_STREAMER2 . $_POST["selected_size"] .MJPG_STREAMER3;
	$output=shell_exec($command);

	// リロード時の二重送信を防ぐために、自分自身に一度 GET を発行する（と、リロードされても POST がでない）
	header("Location: " . $_SERVER['SCRIPT_NAME']."?on=1&selected_device=".$_POST["selected_device"]."&selected_size=".$_POST["selected_size"]."&lang=".$_POST["lang"]);
} else {
	// GET
}

$selected_device = "";
$selected_size = "";
if (isset($_POST["selected_device"]) && !is_null($_POST["selected_device"])){
	$selected_device = $_POST["selected_device"];
}
if (isset($_GET["selected_device"]) && !is_null($_GET["selected_device"])){
	$selected_device = $_GET["selected_device"];
}
if (isset($_POST["selected_size"]) && !is_null($_POST["selected_size"])){
	$selected_size = $_POST["selected_size"];
}
if (isset($_GET["selected_size"]) && !is_null($_GET["selected_size"])){
	$selected_size = $_GET["selected_size"];
}
?>


<?php show_html_head(TITLE) ?>

<SCRIPT language="JavaScript">
	function selecter_select(){
		if ("<?= $selected_device ?>" != ""){
	    $('#selected_device').val("<?= $selected_device ?>");
	  }else{
	    $('#selected_device').val("<?= $video_devices[0] ?>");	  	
	  }
		if ("<?= $selected_size ?>" != ""){
	    $('#selected_size').val("<?= $selected_size ?>");
	  }
    //jquery mobile用の処理
    $('select').selectmenu('refresh',true);
  }

	function setpicurl() {
  	document.getElementById("a").setAttribute("src", "http://<?php echo $_SERVER['SERVER_ADDR'];?>:9000/?action=stream");
	}
	setTimeout("setpicurl()",500)
</SCRIPT>

<body onLoad="selecter_select()">

<div data-role="page" id="new"> 
	  
<div data-role="header" data-position="fixed">
    <h1><?= TITLE ?></h1>
    <a href="../../index.php"><?= $wordsdef[$lang]['return'] ?></a>
    <a href="#lang_panel" data-role="button" data-inline="true"><i class="fa fa-language fa-lg"></i></a>
</div>

<?php if ($_GET["on"] == 1): ?>
	  <div style="text-align: center; margin-top: 30px;"><img id="a" src="http://<?= $_SERVER['SERVER_ADDR'] ?>:9000/?action=stream" alt="image"  ></div>
<?php endif ?>
  
  <div data-role="content" data-theme="c" class="no-cache">
  	<p><?= $wordsdef[$lang]['explain'] ?></p>
  	<form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="post" data-ajax="false">
  		<input type="hidden" name="lang" value="<?= $lang ?>">
  		<div data-role="fieldcontain">
				<label for="selected_size">
					<?= $wordsdef[$lang]['movie_size'] ?>
				</label>
				<select name="selected_size" id="selected_size" data-inline="true" data-native-menu="true">
					<option value="160x120">160x120</option>
					<option value="320x240">320x240</option>
					<option value="640x480">640x480</option>
	 			</select>
	 		</div>

			<div data-role="fieldcontain">
				<label for="selected_device">

<?php if(count($video_devices) == 0) :?>
					<?= $wordsdef[$lang]['no_camera'] ?>
<?php else :?>
					<?= $wordsdef[$lang]['camera'] ?>
<?php endif ?>

				</label>

				<select name="selected_device" id="selected_device" data-inline="true" data-native-menu="true">
<?php foreach ($video_devices as $key => $value): ?>
				<option value="<?= $value ?>"><?= $value ?></option>
<?php endforeach ?>

				</select>
			</div><!-- fieldcontain -->	
			<input type="submit" value="<?= $wordsdef[$lang]['submit'] ?>" />
		</form>

  </div>

<?php show_html_jquery_footer(); ?>

	<!-- lang_panel  -->
	<div data-role="panel" id="lang_panel" data-position="right" data-theme="b">
	  <div class="panel-content">
	    <h3><?= $wordsdef[$lang]['select_lang'] ?></h3>
	    <ul data-role="listview">
	      <li><a href="index.php?lang=en" rel="external">English</a></li>
	      <!-- <li><a href="index.php?serial_id=<?= $_GET['serial_id'] ?>&lang=zh" rel="external">汉语</a></li> -->
	      <li><a href="index.php?lang=ja" rel="external">日本語</a></li>
	    </ul>
	    <a href="#demo-links" data-rel="close" data-role="button" data-theme="c" data-icon="delete" data-inline="true"><?= $wordsdef[$lang]['return'] ?></a>
	  </div><!-- /content wrapper for padding -->
	</div><!-- /lang_panel -->
</div> <!-- page -->

</body>
</html>
