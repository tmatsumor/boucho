<html>
<head><title></title></head>
<frameset cols="20%,*,20%,0">
<frame src="left.htm" name="left" title="左フレーム" scrolling="auto">
<frame src="center.htm" name="center" title="中フレーム" scrolling="no">
<frame src="right.htm" name="right" title="右フレーム" scrolling="yes">
<?php
echo "<frame src='bottom.php".strstr($_SERVER["REQUEST_URI"], '?')."' name='bottom' scrolling='no'>";
echo "</frameset></html>";
?>

