<?php
//===============================================================
// 共通処理
//===============================================================
require_once("common.php");
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
	header("Location: " . $_SERVER['SCRIPT_NAME']."?on=1&selected_device=".$_POST["selected_device"]);
} else {
	// GET
}

$selected_device = "";
if (isset($_POST["selected_device"]) && !is_null($_POST["selected_device"])){
	$selected_device = $_POST["selected_device"];
}
if (isset($_GET["selected_device"]) && !is_null($_GET["selected_device"])){
	$selected_device = $_GET["selected_device"];
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
</div>

<?php if ($_GET["on"] == 1): ?>
	  <div style="text-align: center; margin-top: 30px;"><img id="a" src="http://<?= $_SERVER['SERVER_ADDR'] ?>:9000/?action=stream" alt="image"  ></div>
<?php endif ?>
  
  <div data-role="content" data-theme="c" class="no-cache">
  	<form action="<?= $_SERVER['SCRIPT_NAME'] ?>" method="post" data-ajax="false">
		<div data-role="fieldcontain">
			<label for="selected_size">
				画像サイズを選択してください
			</label>
			<select name="selected_size" id="selected_size" data-native-menu="true">
				<option value="160x120">160x120</option>
				<option value="320x240">320x240</option>
				<option value="640x480">640x480</option>
 			</select>

			<label for="selected_device">

<?php if(count($video_devices) == 0) :?>
				カメラがありません
<?php else :?>
				カメラを選択してください
<?php endif ?>

			</label>

			<select name="selected_device" id="selected_device" data-native-menu="true">
<?php foreach ($video_devices as $key => $value): ?>
				<option value="<?= $value ?>"><?= $value ?></option>
<?php endforeach ?>

			</select>
		</div><!-- fieldcontain -->	
		<input type="submit" value="選択" />
	</form>

  </div>

<?php show_html_jquery_footer(); ?>
</div> <!-- page -->

</body>
</html>
