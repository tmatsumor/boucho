<html>
<head><title></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php
ini_set("display_errors", On);
error_reporting(E_ALL);
require("./kansuji/kansuji.php");
$range = $_GET['range'];
$vcd = $_GET['vcd'];

if(isset($range)) {
    $link = mysqli_connect('localhost','boucho','S9gxcNuK','boucho');
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    $q  = " select a.*, b.hin_al_nm, c.vil_nm from (";
    $q .= " select vil_cd, replace(replace(tanni, '数量なし', ''), '単位なし', '') as tanni, ";
    $q .= " hin_al_cd, value from sanbutsu where vil_cd between ";
    $q .= str_replace(',', ' AND ', $range).") a left outer join hin_alias b ";
    $q .= " on a.hin_al_cd = b.hin_al_cd left outer join village c ";
    $q .= " on a.vil_cd = c.vil_cd ";
    $s = "";
    $a = array();
    if ($result = mysqli_query($link, $q)) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row["kansuji"] = kansuji($row["value"]);
            $a[] = $row;
        }
    }
    $json_array = json_encode($a);
    mysqli_free_result($result);
    mysqli_close($link);
}
?> 
<script type="text/javascript">
//    var arr = <?php echo $json_array; ?>;

    function loadText(arr){
        var vcd = $.unique($.map(arr, el => el["vil_cd"]));
        vcd.forEach((cd)=>{
            const tx = $.grep(arr, el => el["vil_cd"] == cd)
                        .map(el => el["hin_al_nm"] + " " + el["kansuji"]
                                 + el["tanni"]).join(" ").replace(/\s\s/g, " ");
            const vnm = $.grep(arr, el => el["vil_cd"] == cd)[0]["vil_nm"];
            $("#text").append($("<p>").html(vnm +"　"+ tx).attr("id", "to" + cd)
                .css({"writing-mode": "vertical-rl", "margin": "10% auto", "height": "80%"}));
        });
        var to = location.search.match(/&vcd=(\d+)/)[1];
        $("html,body").animate({scrollTop:$('#to' + to).offset().top - 20});
    }
</script>
</head>
<body onload='loadText(<?php echo $json_array; ?>)'>
<div id="text">
</body>
</html>
