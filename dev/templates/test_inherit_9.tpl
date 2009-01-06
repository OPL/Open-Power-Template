<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>
<opt:extend file="test_inherit_9a.tpl" escaping="yes">
	<opt:snippet name="foo">
		<p>This should be escaped: {$htmlContent}</p>
		<opt:parent />
	</opt:snippet>
</opt:extend>