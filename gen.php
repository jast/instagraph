<?php

define('GRAPH_READ_TIMEOUT', 10);

$type = isset($_POST['type']) ? $_POST['type'] : NULL;
$def = isset($_POST['def']) ? $_POST['def'] : NULL;
$private = isset($_POST['private']) ? ($_POST['private'] ? 1 : 0) : 0;

if ($type != 'graph' && $type != 'digraph')
	die("Nuh-uh. Use one of the checkboxes.");

if (strlen($def) > 16384)
	die("Graph too big.");

$hash = sha1($def);

require('config.php');
mysql_connect($config['db_host'], $config['db_user'], $config['db_pass']);
mysql_select_db($config['db_name']);

function get_one($q)
{
	$res = mysql_query($q);
	if (!$res) die("Failed to get stuff from database. Sorry!");
	if (mysql_num_rows($res) == 0) return NULL;
	return mysql_result($res, 0);
}
function id_shorten($id)
{
	static $map = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
	$res = '';
	while ($id > 0) {
		$res = $map[$id & 63] . $res;
		$id >>= 6;
	}
	return $res;
}
function goto_output($id)
{
	if (is_int($id))
		$id = id_shorten($id);
	echo <<<EOR
<!DOCTYPE HTML>
<html><head><title>Your graph</title></head><body>
<p>Graph is ready! (<a href="$id.png">Direct link</a>)
<p><img src="$id.png">
<p><a href="index.php">Generate another graph</a>
</body></html>
EOR;
	exit;
}

if ($graph_id = get_one("SELECT graph_id FROM graphs WHERE graph_code = '$hash'")) {
	goto_output(intval($graph_id));
}

# build graph
$def = "$type G {\n$def\n}\n";
$engine = ($type == 'graph') ? 'neato' : 'dot';

$p = proc_open("/usr/bin/$engine -Tpng", array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
if (!is_resource($p)) die("Couldn't start graphing engine, sorry!");
fwrite($pipes[0], $def);
fclose($pipes[0]);

$read_arr = array($pipes[1]);
$write_arr = NULL;
$err_arr = NULL;
$read_poll = stream_select($read_arr, $write_arr, $err_arr, GRAPH_READ_TIMEOUT);
if ($read_poll === false): ?>
<p>Failed to read graph from graph engine due to an internal error. Sorry!</p>
<?php
	proc_terminate($p);
	exit;
endif;
if ($read_poll == 0): ?>
<p>Graph engine took to long to generate the graph, aborting.</p>
<?php
	proc_terminate($p);
	exit;
endif;
$png = stream_get_contents($pipes[1]);
fclose($pipes[1]);

$err = stream_get_contents($pipes[2]);
fclose($pipes[2]);

$res = proc_close($p);
if ($res != 0): ?>
<p>Couldn't create graph, sorry! Error message follows:
<pre><?php echo htmlspecialchars($err); ?></pre>
<p>The graph definition was:
<pre><?php echo htmlspecialchars($def); ?></pre>
<?php
	exit;
endif;

$png = mysql_real_escape_string($png);
$def = mysql_real_escape_string($def);
$author = mysql_real_escape_string($_SERVER['REMOTE_USER']);

$r = mysql_query("INSERT INTO graphs (graph_code, graph_def, private, graph_img, author) VALUES('$hash', '$def', $private, '$png', '$author')");
if ($r != 1) die("Failed saving graph in database, sorry!");
goto_output($private ? $hash : mysql_insert_id());

