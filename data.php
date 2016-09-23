 <?php
include("db.php");
include("func.php");
$arr = getsp(365);
$ans = getans($arr);
$sort = sorter($ans);
$json = json_encode(prepchart($sort), JSON_NUMERIC_CHECK);
echo $json;
?>

