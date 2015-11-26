<?php
	$link = mysqli_connect("localhost", "", "", "");
	mysqli_set_charset($link, "utf8");
	function quakename($name){
		$name = str_replace("^1", "", $name);
		$name = str_replace("^2", "", $name);
		$name = str_replace("^3", "", $name);
		$name = str_replace("^4", "", $name);
		$name = str_replace("^5", "", $name);
		$name = str_replace("^6", "", $name);
		$name = str_replace("^7", "", $name);
		$name = str_replace("^8", "", $name);
		$name = str_replace("^9", "", $name);
		$name = str_replace("^0", "", $name);
		return $name;
	}
?>
