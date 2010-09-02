<?xml version="1.0" ?>
<opt:root xmlns:opt="http://xml.invenzzia.org/opt">
<opt:load template="snippets.tpl" />
<opt:prolog />
<opt:dtd template="xhtml10strict" />
{@formInvalidFieldRowClass is 'error'} <!-- for form displaying -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$title} - Sample guestbook</title>
	<link rel="stylesheet" href="parse:$global.baseHref~'design/design.css'" type="text/css" />
	<!--[if IE 6]>
	<link rel="stylesheet" href="parse:$global.baseHref~'/design/ie6.css'" type="text/css" />
	<![endif]-->
</head>
<body>
<div id="header">
	<h1>OPT Guestbook</h1>
	<h3>A sample guest book written with Open Power Template 2</h3>
</div>

<div id="content">
	<opt:use snippet="content">
	<p class="error">We are sorry, but there is nothing to be displayed.</p>
	</opt:use>
</div>

<div id="footer">
	<p>Copyright {u:entity('copy')} <a href="http://www.invenzzia.org">Invenzzia Group</a> {range('2009')}</p>
	<p>Distributed under <a href="http://www.invenzzia.org/license/new-bsd">New BSD License</a></p>
</div>
</body>
</html>
</opt:root>
