<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:extend file="test_inherited_a.tpl" dynamic="yes">
	<opt:snippet name="header">
		<h1>Dynamic inheritance</h1>
	</opt:snippet>

	<opt:snippet name="content">
		<p>This file ilustrates the dynamic inheritance. The inherited template is chosen by the script
		from the URL. However, note that the support for this is not yet completed. The illusion that
		everything perfectly changes on demand is done, because CM_REBUILD is on.</p>
	</opt:snippet>
</opt:extend>