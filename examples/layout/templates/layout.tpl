<?xml version="1.0" ?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">
	<opt:prolog standalone="no" />
	<opt:dtd template="xhtml10strict" />
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>OPT Layout example</title>

	<!-- an example, how to use opt:selector to create a dynamic CSS chooser -->
	<opt:selector name="css">
		<opt:equals value="standard">
			<link rel="stylesheet" href="main.css" type="text/css" media="screen" />
			<link rel="stylesheet" href="text.css" type="text/css" media="screen" />
		</opt:equals>
		<opt:equals value="printable">
			<link rel="stylesheet" href="printable.css" type="text/css" media="print" />
		</opt:equals>
		<opt:default>
			<link rel="stylesheet" href="parse:$css.file" type="text/css" />
		</opt:default>
	</opt:selector>
</head>
<body>
<div id="header">
	<h1>Layout example</h1>
	<p>This example file shows, how to manage the more advanced layouts with OPT views
	and sections.</p>
	<p>Select a module:</p>
	<ol>
		<li><a href="index.php?cmd=1">Module A</a></li>
		<li><a href="index.php?cmd=2">Module B</a></li>
		<li><a href="index.php?cmd=3">Module C</a></li>
		<li><a href="index.php?cmd=4">Module A + C</a></li>
		<li><a href="index.php?cmd=5">Module A + B + C</a></li>
	</ol>
</div>
<div id="content">
	<opt:section name="modules">
		<opt:include from="modules">
			<p>We are sorry, but the requested view has not been found.</p>
		</opt:include>
	</opt:section>
</div>
<div id="footer">
	<p>Copyright {u:entity('copy')} Invenzzia Group 2009</p>
</div>
</body>
</html>
</opt:root>