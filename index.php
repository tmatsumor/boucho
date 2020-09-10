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
	$s .= liFile($fst["hin_cd"], $fst["hin_nm"]);
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
	$s .= liFile($row["hin_cd"], $row["hin_nm"]);
	$mon = $row["mon_cd"];
	$kou = $row["kou_cd"];
	$mok = $row["mok_cd"];
}
$s .= "</ul></li></ul></li></ul></li></ul></li>";

function liFolder($txt)
{
    return "<li class='closed'><span class='folder'>".$txt."</span><ul>";
}

function liFile($cd, $nm)
{
	return "<li class='closed'><a href='./map/index.php?cd=".$cd."&nm=".$nm."' target='_blank' style='cursor:pointer'>".$nm."</a></li>";
}
echo $s."</div></body></html>";
mysqli_free_result($result);
mysqli_close($link);

?> 

