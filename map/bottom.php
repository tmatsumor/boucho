<html>
<head><title></title>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

var fL = window.parent.frames['left'];
var fR = window.parent.frames['right'];
var fC = window.parent.frames['center'];
var fB = window;


function loadFrames(){
		eval(document.getElementById('map').value);
		top.frames['center'].loadMap(map);
		
		// ここでtable object生成
		eval(fB.document.getElementById("table").value);
		var st = "<table border=1 bordercolor='#d3d3d3' cellspacing=0 cellpadding=5 style='text-align:center;' width=100%>";
		st += "<tr bgcolor='#d3d3d3'><th>CD</th><th>村名</th><th>収量</th><th>単位</th></tr><tr>";
		table.forEach(function(row){
			st += "<td>" + row[1] + "</td>";
			st += "<td>" + row[0].substr(0, row[0].length - 1) + "</td>";
			st += "<td>" + row[2] + "</td>";
			st += "<td>" + row[3] + "</td>";
			st += "</tr><tr>";
		});
		st = st.substr(0, st.length - 4) + "</table>";
		fR.document.getElementById('divTable').innerHTML = st;
		


		var hinName = decodeURIComponent(location.search.match(/nm=(.*?)(&|$)/)[1]);
		var hinKana = decodeURIComponent(location.search.match(/kn=(.*?)(&|$)/)[1]);
		var sk = "<br /><center><ruby style='font-size:250%;padding:15px;'><rb>" + hinName + "</rb>";
		sk += (hinName != hinKana)? "<rp>（</rp><rt>" + hinKana + "</rt><rp>）</rp>" : "";
		sk += "</ruby><br />";
		
		// ここでkisaimei object作成
		eval(fB.document.getElementById("kisaimei").value);
		sk += "<p style='line-height:140%'>";
		kisaimei.forEach(function(row){
			sk += row[0] + "（" + row[1] + "） ";
		});
		sk += "</p></center>";
		fL.document.getElementById('divKisaimei').innerHTML = sk;

		// ここでsummary object作成
		eval(fB.document.getElementById("summary").value);
		var ss = "<table border=1 bordercolor='#d3d3d3' cellspacing=0 cellpadding=5  style='text-align:center;' width=100%>";
		ss += "<tr bgcolor='#d3d3d3'><th>件数</th><th>合計</th><th>平均</th><th>SD</th><th>単位</th></tr><tr>";
		summary.forEach(function(row){
			ss += "<td>" + row[0] + "</td>";
			ss += "<td>" + row[1] + "</td>";
			ss += "<td>" + row[2] + "</td>";
			ss += "<td>" + row[3] + "</td>";
			ss += "<td>" + row[4] + "</td>";
			ss += "</tr><tr>";
		});
		ss = ss.substr(0, ss.length - 4) + "</table>";
		fL.document.getElementById('divSummary').innerHTML = ss;


		// ここで群ごとの件数をカウント
		var sg = "<br /><table border=1 bordercolor='#d3d3d3' cellspacing=0 cellpadding=5  style='text-align:center;' width=100%>";
		sg += "<tr bgcolor='#d3d3d3'><th>大野郡</th><th>吉城郡</th><th>益田郡</th></tr>";
		var ogun = 0;
		var ygun = 0;
		var mgun = 0;
		map.forEach(function(row){
			if(row[1] <= 137){
				ogun++;
			}else if(row[1] >= 316){
				mgun++;
			}else{
			    ygun++;
			}
		});
		var sum = ogun + ygun + mgun;
		sg += "<td>" + ogun + "</td>";
		sg += "<td>" + ygun + "</td>";
		sg += "<td>" + mgun + "</td></tr><tr>";
		sg += "<td>" + Math.round(ogun/sum*100, 0) + ((ogun > 0)? " %" : "") + "</td>";
		sg += "<td>" + Math.round(ygun/sum*100, 0) + ((ygun > 0)? " %" : "") + "</td>";
		sg += "<td>" + Math.round(mgun/sum*100, 0) + ((mgun > 0)? " %" : "") + "</td></tr></table>";
//		fL.document.getElementById('divGun').innerHTML = sg;
		

		google.charts.load("current", {packages:["corechart"]});
		google.charts.setOnLoadCallback(drawChart);
		fL.document.body.onresize = drawChart;

}

function drawChart(){
	eval(fB.document.getElementById("map").value);
	var options = {
		legend: { position: 'none' },
		histogram: {
			//lastBucketPercentile: 5,
		},
		chartArea: { left: '0px', top: '0px', width: "80%", height: "70%" },
		 hAxis: {
            slantedText: false,
            viewWindowMode: 'maximized',
            textPosition:'out'
        }
	}
	// DataView の作成
	var datatable = google.visualization.arrayToDataTable(map);
	var vdata = new google.visualization.DataView(datatable);
	vdata.setColumns([0, 4]);
	var chart = new google.visualization.Histogram(fL.document.getElementById('divHist'));
	chart.draw(vdata, options);
}


</script>

</head>
<body onload="loadFrames()">
<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
$link = mysqli_connect('localhost','boucho','password','boucho');
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$hinCd;
if(isset($_GET['cd'])&&isset($_GET['nm'])) {
	$hinCd = $_GET['cd'];
}else{
	exit();
}

// １つ目
$q1  = " select vil_nm, a.vil_cd, pos_x, pos_y, value, ";
$q1 .= " (value - (";
$q1 .= " select avg(value) from sanbutsu where hin_al_cd in (";
$q1 .= " select hin_al_cd from hin_alias where hin_cd = ".$hinCd.")";
$q1 .= " )) / (select std(value) from sanbutsu where hin_al_cd in ( ";
$q1 .= " select hin_al_cd from hin_alias where hin_cd = ".$hinCd.")";
$q1 .= " ) as symbol, tanni ";
$q1 .= " from (select vil_cd, sum(value) as value, tanni from sanbutsu where hin_al_cd in ( ";
$q1 .= " select hin_al_cd from hin_alias where hin_cd = ".$hinCd.") group by vil_cd, tanni ";
$q1 .= " ) a left outer join village b on a.vil_cd = b.vil_cd ";
$q1 .= " order by value asc ";
$a1 = array("vil_nm", "vil_cd", "pos_x", "pos_y", "value", "symbol", "tanni");
echo sql2HiddenBox($q1, $a1, "map", $link);

// ２つ目
$q2  = " select vil_nm, a.vil_cd, value, tanni from (";
$q2 .= " select vil_cd, sum(value) as value, tanni from sanbutsu where hin_al_cd in ( ";
$q2 .= " select hin_al_cd from hin_alias where hin_cd = ".$hinCd.") group by vil_cd, tanni ";
$q2 .= " ) a left outer join village b on a.vil_cd = b.vil_cd";
$q2 .= " order by a.vil_cd, value desc, tanni ";
$a2 = array("vil_nm", "vil_cd", "value", "tanni");
echo sql2HiddenBox($q2, $a2, "table", $link);

// ３つ目
$q3  = " select hin_al_nm, count from ( ";
$q3 .= " select hin_al_cd, count(*) as count from ( ";
$q3 .= " select * from sanbutsu where hin_al_cd in ( ";
$q3 .= " select hin_al_cd from hin_alias where hin_cd = ".$hinCd.")";
$q3 .= " ) a group by hin_al_cd ";
$q3 .= " ) b left outer join hin_alias c ";
$q3 .= " on b.hin_al_cd = c.hin_al_cd ";
$q3 .= " order by count desc ";
$a3 = array("hin_al_nm", "count");
echo sql2HiddenBox($q3, $a3, "kisaimei", $link);

// ４つ目
$q4  = " select count(*) as count, round(sum(value),1) as sum, ";
$q4 .= " round(avg(value),1) as avg, round(std(value),1) as std, tanni ";
$q4 .= " from ( select * from sanbutsu where hin_al_cd in (";
$q4 .= " select hin_al_cd from hin_alias where hin_cd = ".$hinCd.")";
$q4 .= " ) a group by tanni order by sum desc ";
$a4 = array("count", "sum", "avg", "std", "tanni");
echo sql2HiddenBox($q4, $a4, "summary", $link);

mysqli_close($link);


function sql2HiddenBox($query, $colNames, $inputName, $link)
{
	if ($result = mysqli_query($link, $query)) {
        $s  = "<input type=\"hidden\" id=\"".$inputName."\" value=\"";
	    $s .= "var ".$inputName."=[";
	}
    while ($row = mysqli_fetch_assoc($result)) {
    	$s .= "[";
        foreach ($colNames as $v) {
            $s .= "'". $row[$v]."',";
        }
        $s = rtrim($s, ",");
    	$s .= "],";
    }
    $s = rtrim($s, ",");
    $s .= "];";
    $s .= "\" />";
    mysqli_free_result($result);
    return $s;
}
?> 
</body>
</html>

