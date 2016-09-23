<?php
$start_time = microtime(TRUE);
?>
<!doctype html >
<?php include("func.php");?>
<html lang="en" >
<head>
<title> NRK P3s musikk graph</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="style.css" >
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>

<script type="text/javascript"
src="https://www.google.com/jsapi?autoload={
'modules':[{
    'name':'visualization',
        'version':'1',
        'packages':['corechart']
}]
}"></script>

<?php if($_GET["a"] && $_GET["s"]) : ?>
<script type="text/javascript">
google.setOnLoadCallback(drawChart);

function drawChart() {
<?php 
dcdata("day", "dato", "spilt", songinfo($_GET["a"], $_GET["s"], "0")); 
dcdata("week", "dato", "spilt", songinfo($_GET["a"], $_GET["s"], "1"));
?>
<?php
dcopt("day", "Daglige av spillinger siste månde", "200", "600");
dcopt("week", "Ukentlige av spillinger siste 6 uker", "200", "600");
?>
<?php
dcdraw("day");
dcdraw("week");
?>
};
        </script>
<?php else :?>
<script type="text/javascript"
src="https://www.google.com/jsapi?autoload={
'modules':[{
    'name':'visualization',
        'version':'1',
        'packages':['corechart']
}]
}"></script>

<script type="text/javascript">
google.setOnLoadCallback(drawChart);

function drawChart() {
<?php 
dcdbdata("dbstats", "dato", "artist", "song", prepchartdbday(dbstatsday())); 
dcdata("dbtotal", "dato", "total", prepchartdbtday(dbstatsday())); 
?>
<?php
dcopt("dbstats", "antall sanger og artistar i databasen", "200", "600");
dcopt("dbtotal", "totalt antall spilte sanger", "200", "600");
?>
<?php
dcdraw("dbstats");
dcdraw("dbtotal");
?>
};
        </script>
<?php endif; ?>


</head>
<body>
<div id="doc" class="yui-t7">
  <div id="hd">
    <div id="header"><a href="/"><img src="logo.png"></a></div>
</div>
  <div id="bd">
    <div id="yui-main">
      <div class="yui-b">
        <div class="yui-g">
          <div class="yui-u first">
            <div class="content">

<div id="left">
<?php


if($_GET["a"] && $_GET["s"]){
$song = song($_GET["s"],$_GET["a"]);
print $song;
$songarr = songans($_GET["s"],$_GET["a"]);
$playcount = playcount($_GET["s"],$_GET["a"]);
print "<h3>spilt totalt {$playcount} ganger</h3>";
//print "<a href=\"spotify:search:track:synthetic+romance+artist:cullen+omori\">spotify</a><br>";
$youtubeid = youtubelink($_GET["s"],$_GET["a"]);
//print "Youtube<br><a href=\"https://www.youtube.com/watch?v={$youtubeid}\"><img src=\"http://img.youtube.com/vi/{$youtubeid}/0.jpg\" height=\"100\" Width=\"120\"></a>";
print "<iframe width=\"500\" height=\"275\" src=\"https://www.youtube.com/embed/{$youtubeid}\" frameborder=\"0\" allowfullscreen></iframe>";
}
else{
indexlist(); 
}
?>

</div>
</div>
          </div>
          <div class="yui-u">
            <div class="content">
<?php if($_GET["a"] == null && $_GET["s"] == null) : ?> 
<div id="dbstats"> 
</div>
<p></p>
<div id="dbtotal"> 
</div>
<?php endif; ?>
<?php if($_GET["a"] && $_GET["s"]) : ?>
              
<div id="day">
</div>
<p></p>
<div id="week">
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
    <div class="yui-b">
      <div id="secondary">
<div id="newest">
<p>laster...</p>
    </div>
  </div>
  <div id="ft">
    <div id="footer">
<?php
$end_time = microtime(TRUE);
$time_taken = $end_time - $start_time;
$time_taken = round($time_taken,5);
echo 'Page generated in '.$time_taken.' seconds.';
echo "</br> &copy; Erviker IT" 
?>
</div>
</br>
</br>
</div>
  </div>
</div>

<script language="javascript" type="text/javascript">
function loadnew(){
        $('#newest').load('latest.php',function () {
        //                $(this).unwrap();
                                    });
}
setInterval(function(){loadnew()}, 10000);
loadnew();
</script>
</body>
</html>
