<?php
function searchlist($arr){
    var_dump($arr);
    switch($arr["type"]){
    case 1:
        print "song";
        break;
    case 2:
        print "artist";
        break;
    }


}

function search($post){
    include("db.php");
    if($post["type"] = "1"){
        $s["type"] = 1;
        $sql = "SELECT * FROM `Song` WHERE `Song` LIKE '{$post["name"]}'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $s["id"] = (int)$row["id"];
                $s["song"] = $row["Song"];
            }
        }
        $i = 0;
        $sql = "SELECT DISTINCT `artist`,`song`FROM `speleliste`WHERE `song` = '{$s["id"]}'"; 
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $s["artist"][$i]["id"] = (int)$row["artist"];
                $nw = songans($s["id"], $row["artist"]);
                $s["artist"][$i]["name"] = $nw["artist"]["name"];
                $s["artist"][$i]["playcount"] = playcount($s["id"], $row["artist"]);
                $i++;
            }
        } 
    }
    if($post["type"] = "2"){
        $s["type"] = 2;
        $sql = "SELECT * FROM `Artist` WHERE `Artist` LIKE '{$post["name"]}'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $s["id"] = (int)$row["id"];
                $s["artist"] = $row["Artist"];
            }
        }
        $i = 0;
        $sql = "SELECT DISTINCT `artist`,`song`FROM `speleliste`WHERE `artist` = '{$s["id"]}'"; 
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()) {
                $s["song"][$i]["id"] = (int)$row["song"];
                $nw = songans($row["song"], $s["id"]);
                $s["song"][$i]["name"] = $nw["song"]["name"];
                $s["song"][$i]["playcount"] = playcount($row["song"], $s["id"]);
                $i++;
            }
        }
    }
    else {
        print "error!";
    }
    return $s;
}

function lastsong(){
    include("db.php");
    $ls = array();
    $sql = "SELECT * FROM `speleliste` ORDER BY id DESC LIMIT 1;";
    $result = $conn->query($sql);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $ls["song"] = $row["song"];
            $ls["artist"] = $row["artist"];
        }
    }
    $ls = songans($ls["song"],$ls["artist"]);
    return $ls;
    $conn->close();
}
function dbstatsday(){
    include("db.php");
    $time = time();
    $day = 60 * 60 * 24;
    $sp = array();
    for ($i = 0; $i < 30; ++$i) {
        $diff = $time - $day;
        $sp[$i]["pc"] = 0;
        $sp[$i]["time"] = $time;
        $sql = "SELECT * FROM `stats` WHERE `time` BETWEEN {$diff} AND {$time} ORDER BY  `stats`.`time` DESC  ";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $sp[$i]["artist"] = $row["artist"];
                $sp[$i]["song"] = $row["song"];
                $sp[$i]["total"] = $row["total"];
                $sp[$i]["time"] = $row["time"];
            }
        }
        $time = $diff;
    }
    return $sp;
    $conn->close();
}
function prepchartdbday($array){
    for ($i = 0; $i < 30; ++$i) {
        $array[$i]["time"] = $array[$i]["time"] - 3600;
        $arr[$i][0] = date("d.m.y", "{$array[$i]["time"]}") ;
        $arr[$i][1] = (int)$array[$i]["artist"];
        $arr[$i][2] = (int)$array[$i]["song"]; 
    }
    $arr = array_reverse($arr);
    return $arr;
}
function prepchartdbtday($array){
    for ($i = 0; $i < 30; ++$i) {
        $arr[$i][0] = date("d.m.y", $array[$i]["time"]) ;
        $arr[$i][1] = (int)$array[$i]["total"];
    }
    $arr = array_reverse($arr);
    return $arr;
}
function dcdbdata($div, $horz, $vert, $vert2, $data) {
    print "

    var data_{$div} = new google.visualization.DataTable();
    data_{$div}.addColumn('string', '{$horz}'); 
    data_{$div}.addColumn('number', '{$vert}'); 
    data_{$div}.addColumn('number', '{$vert2}'); 
    data_{$div}.addRows( ";
    echo(json_encode($data));
    echo ");";
}


function insertdbstats() {
    include("db.php");
    $time = time();
    $dbstats = dbstats();
    $sql = "INSERT INTO `p3musikk`.`stats` (`artist`, `song`, `total`, `time`) VALUES ('{$dbstats["artist"]}', '{$dbstats["song"]}', '{$dbstats["sp"]}', '{$time}');";
    $result = $conn->query($sql);
}
function youtubelink($sid,$aid){
    $songraw = songans($sid,$aid);
    $song = "{$songraw["artist"]["name"]} - {$songraw["song"]["name"]}";
    $songid = yts($song,$key);
    return $songid;
}

function song($sid,$aid) {
    $ans = songans($sid,$aid);
    $url = urlencode($ans["artist"]["name"]);
    $info = "<h1>{$ans["song"]["name"]} - <a href=\"search.php?name={$url}&type=2\">{$ans["artist"]["name"]}</a></h1>";
    return $info;
}

function playcount($sid,$aid){

    include("db.php");
    $sql = "SELECT id FROM speleliste WHERE song = '{$sid}' AND artist = '{$aid}'";
    $result = $conn->query($sql);
    $pcc = $result->num_rows;
    $conn->close();
    return $pcc;    
}

function songans($sid,$aid) {

    include("db.php");

    $nw = array();
    $sql = "SELECT * FROM Song WHERE `id` = '{$sid}' ORDER BY id DESC LIMIT 1;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $nw["song"]["id"] = $row["id"];
            $nw["song"]["name"] = $row["Song"];
        }
    }


    $sql = "SELECT *  FROM `Artist` WHERE `id` = '{$aid}'  LIMIT 1;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $nw["artist"]["id"] = $row["id"];
            $nw["artist"]["name"] = $row["Artist"];
        }
    }

    return $nw;
    $conn->close();

};
function funfacts() {
    $nw = newest();
    $dbstats = dbstats();
    $ls = lastsong();
    $datoen = date("H:m - d.m.y", $nw["sp"]["time"]);
    print "Der er no {$dbstats["song"]} sanger laga av {$dbstats["artist"]} artistar spelt totalt {$dbstats["sp"]}</br> \n";
    print "Siste sang inni databasen er {$nw["song"]["name"]} - {$nw["artist"]["name"]} lakt til {$datoen}<br/>";
    print "siste spelte sang er <a href=\"/?a={$ls["artist"]["id"]}&s={$ls["song"]["id"]}\">{$ls["song"]["name"]} - {$ls["artist"]["name"]}</a>";

}

function dbstats() {
    include("db.php");
    $dbstats = array();
    $sql = "SELECT id FROM Song";
    $result = $conn->query($sql);
    $dbstats["song"] = $result->num_rows;

    $sql = "SELECT id FROM Artist";
    $result = $conn->query($sql);
    $dbstats["artist"] = $result->num_rows;

    $sql = "SELECT id FROM speleliste";
    $result = $conn->query($sql);
    $dbstats["sp"] = $result->num_rows;
    $conn->close();
    return $dbstats;    
}

function newest() {

    include("db.php");

    $nw = array();
    $sql = "SELECT * FROM Song ORDER BY id DESC LIMIT 1;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $nw["song"]["id"] = $row["id"];
            $nw["song"]["name"] = $row["Song"];
        }
    }

    $sql = "SELECT *  FROM `speleliste` WHERE `song` = '{$nw["song"]["id"]}' LIMIT 1;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $nw["sp"]["artist"] = $row["artist"];
            $nw["sp"]["song"] = $row["song"];
            $nw["sp"]["time"] = $row["time"];
        }
    }

    $sql = "SELECT *  FROM `Artist` WHERE `id` = '{$nw["sp"]["artist"]}'  LIMIT 1;";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $nw["artist"]["id"] = $row["id"];
            $nw["artist"]["name"] = $row["Artist"];
        }
    }

    return $nw;
    $conn->close();

};

function dcdata($div, $horz, $vert, $data) {
    print "

    var data_{$div} = new google.visualization.DataTable();
    data_{$div}.addColumn('string', '{$horz}'); 
    data_{$div}.addColumn('number', '{$vert}'); 
    data_{$div}.addRows( ";
    echo(json_encode($data));
    echo ");";
}

function dcopt($div, $title, $h, $w){

    print "
    var options_{$div} = {
    title: '{$title}',
    curveType:'function',
    width:{$w},
    height:{$h},
    vAxis:{viewWindowMode: 'explicit', viewWindow:{ min: 0 }},};";

}

function dcdraw($div) {

    print "
    var chart_{$div} = new google.visualization.LineChart(document.getElementById('{$div}'));
    chart_{$div}.draw(data_{$div}, options_{$div});";
}


function songinfo($a, $s, $mode) {
    switch ($mode) {
    case 0:
        $day =  prepchartday(playbyday($a, $s));
        return $day;
        break;
    case 1:
        $week = prepchartweek(playbyweek($a, $s));
        return $week;
        break;
    case 2:
        $month = playbymonth($a, $s);
        return $month;
        break;
    case 3:
        $year = playbyyear($a, $s);
        return $year;
        break;
    default:
        $day =  prepchartday(playbyday($a, $s));
        return $day;
        break;

    }
}
function indexlist(){
    $arr = getsp(7);
    $ans = getans($arr);
    $sort = sorter($ans);
    print"<h3>TOP 20 siste 7 dagane</h3>"; 
    for ($i = 0; $i < 19; ++$i) {
        echo "<a href=\"?a={$sort[$i]["artist"]}&s={$sort[$i]["sid"]}\">";
        echo "{$sort[$i]["aname"]} - {$sort[$i]["sname"]} (spilt: {$sort[$i]["pc"]})";
        echo "</a> </br> ";
    }
}


function alltime(){
    $arr = getsp(365);
    $ans = getans($arr);
    $sort = sorter($ans);
    print"<h3>TOP 20 siste 1000 dagane</h3>"; 
    for ($i = 0; $i < 19; ++$i) {
        echo "<a href=\"?a={$sort[$i]["artist"]}&s={$sort[$i]["sid"]}\">";
        echo "{$sort[$i]["aname"]} - {$sort[$i]["sname"]} (spilt: {$sort[$i]["pc"]})";
        echo "</a> </br> ";
    }
}

function playbyday($aid, $sid){

    include("db.php");

    $time = strtotime('today midnight') - 10;
    $day = 60 * 60 * 24;

    $sp = array();
    for ($i = 0; $i < 30; ++$i) {
        $diff = $time - $day;
        $sp[$i]["pc"] = 0;
        $sp[$i]["time"] = $time;
        $sql = "SELECT * FROM `speleliste` WHERE `time` BETWEEN {$diff} AND {$time} AND `artist` = {$aid} AND `song` = {$sid} ORDER BY  `speleliste`.`time` DESC  ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $sp[$i]["pc"] = $sp[$i]["pc"] + 1;
                $sp[$i]["time"] = $time;

            }

        }
        $time = $diff;
    }
    return $sp;
    $conn->close();

}
function playbyweek($aid, $sid){

    include("db.php");



    $time = strtotime("last monday midnight") - 10;
    $day = 60 * 60 * 24 * 7;


    $sp = array();
    for ($i = 0; $i < 6; ++$i) {
        $diff = $time - $day;
        $sp[$time]["pc"] = "0";
        $sp[$i]["time"] = $time;
        $sql = "SELECT * FROM `speleliste` WHERE `time` BETWEEN {$diff} AND {$time} AND `artist` = {$aid} AND `song` = {$sid} ORDER BY  `speleliste`.`time` DESC  ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $sp[$i]["pc"] = $sp[$i]["pc"] + 1;
                $sp[$i]["time"] = $time;
            }

        }
        $time = $diff;
    }
    return $sp;
    $conn->close();


}
function playbymonth($aid, $sid){

    include("db.php");



    $time = time();
    $day = 60 * 60 * 12 * 30;


    $sp = array();
    for ($i = 0; $i < 24; ++$i) {
        $diff = $time - $day;

        $sql = "SELECT * FROM `speleliste` WHERE `time` BETWEEN {$diff} AND {$time} AND `artist` = {$aid} AND `song` = {$sid} ORDER BY  `speleliste`.`time` DESC  ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {

                $sp[$i]["pc"] = $sp[$i]["pc"] + 1;
                $sp[$i]["artist"] = $row["artist"];
            }

        }
        $time = $diff;
    }
    return $sp;
    $conn->close();


}
function playbyyear($aid, $sid){

    include("db.php");



    $time = time();
    $day = 60 * 60 * 24 * 365;


    $sp = array();
    for ($i = 0; $i < 1; ++$i) {
        $diff = $time - $day;

        $sql = "SELECT * FROM `speleliste` WHERE `time` BETWEEN {$diff} AND {$time} AND `artist` = {$aid} AND `song` = {$sid} ORDER BY  `speleliste`.`time` DESC  ";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $sp[$i]["pc"] = $sp[$i]["pc"] + 1;
                $sp[$i]["artist"] = $row["artist"];
            }

        }
        $time = $diff;
    }
    return $sp;
    $conn->close();


}

function prepchart($array){
    for ($i = 0; $i < 30; ++$i) {
        $arr[$i][0] = $array[$i]["aname"] ." - ". $array[$i]["sname"] ;
        $arr[$i][1] = $array[$i]["pc"]; 
    }


    return $arr;
}

function prepchartday($array){
    for ($i = 0; $i < 30; ++$i) {


        $arr[$i][0] = date("d.m.y", $array[$i]["time"]) ;
        $arr[$i][1] = $array[$i]["pc"]; 
    }
    $arr = array_reverse($arr);

    return $arr;
}

function prepchartweek($array){
    for ($i = 0; $i < 6; ++$i) {
        $arr[$i][0] = date("W", $array[$i]["time"]) ;
        $arr[$i][1] = $array[$i]["pc"]; 
    }
    $arr = array_reverse($arr);

    return $arr;
}
function prepdown($array){
    for ($i = 0; $i < count($array); ++$i) {
        $arr[$i] = $array[$i]["aname"] ." - ". $array[$i]["sname"] ;
    }


    return $arr;
}

function getans($arr){
    include("db.php");

    foreach ($arr as $key => $value) {
        $sid = $key;
        foreach ($value as $key => $value) {
            if($key == "artist"){$aid = $value;};
        }
        $sql = "SELECT *  FROM `Artist` WHERE `id` = '{$aid}' ";
        $sql2 = "SELECT *  FROM `Song` WHERE `id` = '{$sid}' ";
        $result = $conn->query($sql);
        $result2 = $conn->query($sql2);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $aname =  $row["Artist"];
            }
        }
        if ($result2->num_rows > 0) {
            // output data of each row
            while($row = $result2->fetch_assoc()) {
                $sname =  $row["Song"];

            }

        }
        $arr[$sid]["aname"] = $aname;
        $arr[$sid]["sname"] = $sname;
        $arr[$sid]["sid"] = $sid;
    }
    $conn->close();
    return $arr;
}
function sort_by_order ($a, $b)
{
    return $b['pc'] - $a['pc'] ;
}
function sorter($arr){
    usort($arr, 'sort_by_order');
    return $arr;
};

function getsp($days){

    include("db.php");


    if($days == null){
        $days = "1" ;
    }
    $days = $days * 86400;

    $time = time() - $days;

    $sp = array();
    $sql = "SELECT * FROM `speleliste` WHERE `time` >= '{$time}' ORDER BY  `speleliste`.`time` DESC  ";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $sp[$row["song"]]["pc"] = $sp[$row["song"]]["pc"] + 1;
            $sp[$row["song"]]["artist"] = $row["artist"];
        }
    }
    return $sp;
    $conn->close();

};

function getdata(){

    $json = file_get_contents('http://p3.no/saus/nettradio/onairnow.php');
    $obj = json_decode($json);
    $b = $obj->program->elements;

    if($b[0]->runorder == "present" && $b[0]->type == "Music" ) {
        $artist = $b[0]->contributor;
        $song = $b[0]->title;
    }
    elseif($b[1]->runorder == "present" && $b[1]->type == "Music" ) {
        $artist = $b[1]->contributor;
        $song = $b[1]->title;
    }
    elseif($b[2]->runorder == "present" && $b[2]->type == "Music" ) {
        $artist = $b[2]->contributor;
        $song = $b[2]->title;
    }
    else {
        $artist = NULL;
        $song = NULL;
    }
    return array($artist, $song);

};


function yts($q,$key) {

    set_include_path(get_include_path() . PATH_SEPARATOR . '/var/www/google-api-php-client/src');
    // Call set_include_path() as needed to point to your client library.
    require_once 'google-api-php-client/src/Google/autoload.php';
    require_once 'Google/Client.php';
    require_once 'Google/Service/YouTube.php';
    /*
     * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
     * {{ Google Cloud Console }} <{{ https://cloud.google.com/console }}>
     * Please ensure that you have enabled the YouTube Data API for your project.
     */
    $DEVELOPER_KEY = $key;
    $client = new Google_Client();
    $client->setDeveloperKey($DEVELOPER_KEY);
    // Define an object that will be used to make all API requests.
    $youtube = new Google_Service_YouTube($client);
    try {
        // Call the search.list method to retrieve results matching the specified
        // query term.
        $searchResponse = $youtube->search->listSearch('id,snippet', array(
            'q' => $q,
            'maxResults' => 1,
        ));
        $videos = '';
        $channels = '';
        $playlists = '';
        // Add each result to the appropriate list, and then display the lists of
        // matching videos, channels, and playlists.
        foreach ($searchResponse['items'] as $searchResult) {
            switch ($searchResult['id']['kind']) {
            case 'youtube#video':
                $video = $searchResult['id']['videoId'];
                break;
            }
        }
    } catch (Google_Service_Exception $e) {
        $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    } catch (Google_Exception $e) {
        $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
            htmlspecialchars($e->getMessage()));
    }
    return $video;
}

?>
