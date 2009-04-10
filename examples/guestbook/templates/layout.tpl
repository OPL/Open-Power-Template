<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<opt:root>
<opt:prolog />
<opt:dtd template="xhtml10transitional" />
{@formInvalidFieldRowClass is 'error'} <!-- for form displaying -->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
  <head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>{$global.title} - {$websiteTitle}</title>
  <link rel="stylesheet" href="/design/general.css" type="text/css" />
  <!--[if IE]>
  <link rel="stylesheet" href="/design/ie.css" type="text/css" />
  <![endif]-->
</head>
<body>
  <div id="header">
	<h1>OPT Guestbook</h1>
	<span>A sample guest book written with Open Power Template 2</span>
  </div>

  <div id="content">

	<opt:include view="$view">
		<p class="error">We are sorry, but there is nothing to be displayed.</p>
	</opt:include>

  </div>

  <div id="footer">
    <p>&copy; <a href="http://www.invenzzia.org">Invenzzia Group</a> 2009</p>
    <p>Distributed under <a href="http://www.invenzzia.org/license/new-bsd">New BSD License</a></p>
  </div>
</body>
</html>
</opt:root>