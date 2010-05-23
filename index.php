<!DOCTYPE HTML>
<html><head>
	<title>Graph generator thingy</title>
</head><body>
<h1>Graph generator</h1>
<form method="post" action="gen.php">
<p>Type: <input type="radio" name="type" value="graph" id="type_g"> <label
for="type_g">undirected</label> or <input type="radio" name="type"
value="digraph" id="type_d" checked="checked">
<label for="type_d">directed</label> graph

<p>Definition (contents of a dot graph {} container):<br>
<textarea name="def" rows="20" cols="50"></textarea>

<p><input type="checkbox" name="private" value="1" id="priv"> <label
for="priv">private (non-guessable URL)</label>

<p><input type="submit">
</form>
</body></html>
