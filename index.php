<!DOCTYPE HTML>
<html><head>
	<title>Graph generator thingy</title>
</head><body>
<h1>Graph generator</h1>

<p>Here you can generate your own graphs. Generated graphs will be stored
indefinitely and the generated image URLs will not change. Once generated, a
graph cannot be changed, but you can generate a new one.

<form method="post" action="gen.php">
<p>Type: <input type="radio" name="type" value="graph" id="type_g"> <label
for="type_g">undirected</label> or <input type="radio" name="type"
value="digraph" id="type_d" checked="checked">
<label for="type_d">directed</label> graph

<p>Definition (without the surrounding <code>[di]graph {}</code> thing): (<a
href="http://en.wikipedia.org/wiki/DOT_language">syntax</a>, <a
href="index.php?example=1">example</a>)<br>
<textarea name="def" rows="20" cols="50"><?php if ($_GET['example']):
?>Sleep -&gt; Work [label="zombie"]
Work -&gt; TV [label="zombie"]
TV -&gt; Sleep [label="zombie"]
<?php endif; ?></textarea>

<p><input type="checkbox" name="private" value="1" id="priv"> <label
for="priv">private (non-guessable URL)</label>

<p><input type="submit">
</form>
</body></html>
