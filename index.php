<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
$link = mysqli_connect('localhost','boucho','password','boucho');
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
$q  = " select mon.mon_cd, mon.mon_nm, kou.kou_cd, kou.kou_nm, ";
$q .= " mok.mok_cd, mok.mok_nm, hin_cd, hin_nm from hinmoku ";
$q .= " left outer join mok on hinmoku.mok_cd = mok.mok_cd ";
$q .= " left outer join kou on mok.kou_cd = kou.kou_cd ";
$q .= " left outer join mon on kou.mon_cd = mon.mon_cd order by mok.mok_cd ";

$s = "";
$mon;
$kou;
$mok;
if ($result = mysqli_query($link, $q)) {
	echo "<html><head><title></title>";
	echo "	<script src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>";
	echo "<link rel='stylesheet' href='./lib/jquery.treeview.css' />";
	echo "<script src='../lib/jquery.treeview.js' type='text/javascript'></script>";
	echo "<script type='text/javascript'>";
	echo " $(document).ready(function(){ $('#ulTree').treeview({animated:'fast'})})";
	echo "</script>";
	echo "</head>";
	echo "<body bgcolor='#f0f8ff'>";

	$fst = mysqli_fetch_assoc($result);
	$s .= "<br /><center><h1>『防長風土注進案』産物・産業記載データベース</h1></center><br /><br />";
	$s .= "<div style='width:500px;margin: auto;'><ul id='ulTree' class='filetree'><ul>";
	$s .= liFolder($fst["mon_nm"]);
	$s .= liFolder($fst["kou_nm"]);
	$s .= liFolder($fst["mok_nm"]);
	$s .= liMap($fst["hin_cd"], $fst["hin_nm"]);
	$mon = $fst["mon_cd"];
	$kou = $fst["kou_cd"];
	$mok = $fst["mok_cd"];
}
while($row = mysqli_fetch_assoc($result)){
	$smn = liFolder($row["mon_nm"]);
	$sku = liFolder($row["kou_nm"]);
	$smk = liFolder($row["mok_nm"]);
	
	if($row["mon_cd"] != $mon){
		$s .= "</ul></li></ul></li></ul></li></ul><ul>".$smn;
		$s .= $sku.$smk;
	}else if($row["kou_cd"] != $kou){
		$s .= "</ul></li></ul></li></ul><ul>".$sku.$smk;
	}else if($row["mok_cd"] != $mok){
		$s .= "</ul></li></ul><ul>".$smk;
	}
	$s .= liMap($row["hin_cd"], $row["hin_nm"]);
	$mon = $row["mon_cd"];
	$kou = $row["kou_cd"];
	$mok = $row["mok_cd"];
}
$s .= "</ul></li></ul></li></ul></li></ul></li>";

function liFolder($txt)
{
    return "<li class='closed'><span class='folder'>".$txt."</span><ul>";
}

function liFile($nm, $url)
{
	return "<li class='closed'><a href='".$url."' target='_blank' style='cursor:pointer'>".$nm."</a></li>";
}

function liMap($cd, $nm)
{
	return liFile($nm, "./map/index.php?cd=".$cd."&nm=".$nm);
}

function liPage($cd, $nm, $range)
{
	return liFile($nm, "./page.php?range=".$range."&vcd=".$cd);
}

$sai = array("大島"=>"1,30","奥阿武"=>"31,49","奥山代"=>"50,65","前山代"=>"66,78","上関"=>"79,104","熊毛"=>"105,129","都濃"=>"130,148","三田尻"=>"149,179","徳地"=>"180,199","山口"=>"200,221","小郡"=>"222,237","舟木"=>"238,263","吉田"=>"264,278","美禰"=>"279,289","先大津"=>"290,302","前大津"=>"303,314","当島"=>"315,326");
$vil = array(array());

if ($res2 = mysqli_query($link, " select vil_cd, vil_nm from village ")) {
	$lim = array_values(array_map(function($x){ return preg_replace("/^.+,/", "", $x); }, $sai));
	while($row = mysqli_fetch_assoc($res2)){
		$idx = count($vil) - 1;
		if($row["vil_cd"] >= $lim[$idx]){
			$vil[] = array();
		}
		$vil[$idx][] = array($row["vil_cd"], $row["vil_nm"], array_values($sai)[$idx]);
	}
}

$s .= liFolder("産物記載");
foreach (array_keys($sai) as $k) {
	$s .= liFolder($k);
	foreach (array_shift($vil) as $v){ $s .= liPage($v[0], $v[1], $v[2]); }
	$s .= "</ul></li>";
}
$s .= "</ul></li>";

echo $s."</div></body></html>";
mysqli_free_result($result);
mysqli_free_result($res2);
mysqli_close($link);

?> 

