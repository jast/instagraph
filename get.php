<?php

require('config.php');
mysql_connect($config['db_host'], $config['db_user'], $config['db_pass']);
mysql_select_db($config['db_name']);

function id_lengthen($id)
{
	static $map = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
	$res = 0;
	for ($i = 0; $i < strlen($id); $i++) {
		$res <<= 6;
		if (FALSE === ($j = strpos($map, $id[$i]))) die("Invalid ID.");
		$res += $j;
	}
	return $res;
}

$id = $_GET['id'];
if (!$id) die("No ID given.");
if (strlen($id) == 40)
	$q = "graph_code = '".mysql_real_escape_string($id)."'";
else
	$q = "graph_id = ". id_lengthen($id) ." AND private = 0";

function get_all($q)
{
	$res = mysql_query($q);
	if (!$res) die("Failed to get stuff from database. Sorry!");
	$arr = array();
	while ($row = mysql_fetch_assoc($res)) $arr[] = $row;
	return $arr;
}

$d = get_all("SELECT graph_img FROM graphs WHERE $q");
if (!$d) die("Graph doesn't exist.");

header("Content-Type: image/png");
print $d[0]['graph_img'];
