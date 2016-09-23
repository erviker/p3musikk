 <?php
include("db.php");
include("func.php");
//$arr = getsp(365);
//$ans = getans($arr);
$test1 = playbyday("61","58");


$test2 = json_encode(prepchartday($test1), JSON_NUMERIC_CHECK);

echo($test2);

?>

