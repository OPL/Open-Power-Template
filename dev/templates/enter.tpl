<?xml version="1.0" encoding="UTF-8"?>
<opt:root>
<opt:prolog standalone="no" />
<opt:dtd template="xhtml10strict" />
<html xmlns="http://www.w3.org/1999/xhtml" lang="pl_PL" xml:lang="pl_PL">
<head>
	<base parse:href="$baseHref" />
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta http-equiv="Content-Language" content="pl" />
	<link type="text/css" rel="stylesheet" href="./public/step/enterStyle.css" />
	<title>{$pagetitle}</title>
</head>
<body>
<div id="page">
	<img src="./public/step/images/welcome.png" id="welcome" alt="Witamy!" />
	<img src="./public/step/images/enter.png" id="logo" alt=" " />
	<div id="links">
		<ul id="list" opt:selector="linki">
			<opt:active><li><span class="link active">{$linki.text}</span></li></opt:active>
			<opt:default><li><a parse:href="$linki.link" class="link">{$linki.text}</a></li></opt:default>
		</ul>
</div>
</body>
</html>
</opt:root>