<?php


include("db.php");
include("func.php");




$data = getdata();

$data[0] = $conn->real_escape_string($data[0]);
$data[1] = $conn->real_escape_string($data[1]);





if($data[0] != NULL) {

	$sql = "SELECT id, Artist FROM Artist WHERE Artist = '{$data[0]}' ";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$artistid = $row["id"];
		}
	} else {
		$artistid = 0;
	}

	$sql = "SELECT id, Song FROM Song WHERE Song = '{$data[1]}' ";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$songid = $row["id"];
		}
	} else {
		$songid = "0";
	}


	if($artistid == "0" || $songid == "0"){
		if($artistid == "0") {
			$sql = "INSERT INTO Artist (Artist) VALUES ('{$data[0]}')";

			if ($conn->query($sql) === TRUE) {
				echo "New artist record created successfully \n";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}

		}
		if($songid == "0") {
			$sql = "INSERT INTO Song (Song) VALUES ('{$data[1]}')";

			if ($conn->query($sql) === TRUE) {
				echo "New song record created successfully\n";
			} else {
				echo "Error: " . $sql . "<br>" . $conn->error;
			}

		}
	}
	$sql = "SELECT id, Artist FROM Artist WHERE Artist = '{$data[0]}' ";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$artistid = $row["id"];
		}
	} else {
		$artistid = 0;
	}

	$sql = "SELECT id, Song FROM Song WHERE Song = '{$data[1]}' ";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$songid = $row["id"];
		}
	} else {
		$songid = "0";
	}



	$sql = "SELECT artist, song FROM speleliste ORDER BY id DESC LIMIT 1";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$lastsongid = $row["song"];
			$lastartistid = $row["artist"];
		}
	} else {
		print "ERROR!";
	}
	if($artistid != $lastartistid || $songid != $lastsongid) {
        $time = time();
        $ans = songans($songid, $artistid);
		$sql = "INSERT INTO speleliste (artist, song, time) VALUES ('{$artistid}', '{$songid}', '{$time}')";
		if ($conn->query($sql) === TRUE) {
			echo "New playlist record created successfull {$ans["song"]["name"]}({$ans["song"]["id"]})-{$ans["artist"]["name"]}({$ans["artist"]["id"]}) \n";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}

	}
}
$conn->close();




?>
