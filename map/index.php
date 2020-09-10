<html>
<head><title></title></head>
<frameset cols="20%,*,20%,0">
<frame src="left.htm" name="left" title="左フレーム" scrolling="auto">
<frame src="center.htm" name="center" title="中フレーム" scrolling="no">
<frame src="right.htm" name="right" title="右フレーム" scrolling="yes">
<?php
$hinCd;
if(isset($_GET['cd'])&&isset($_GET['nm'])) {
	$hinCd = $_GET['cd'];
	$hinNm = $_GET['nm'];
	$hinKn = $_GET['kn'];
}else{
	exit();
}
echo "<frame src='bottom.php?cd=".$hinCd."&nm=".$hinNm."&kn=".$hinKn."' name='bottom' scrolling='no'>";
echo "</frameset></html>";
?>

